<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiSuggestionService
{
    private string $apiKey;

    private array $venueImages = [
        'rosewood'   => 'https://commons.wikimedia.org/wiki/Special:FilePath/Vattanac_Capital_Tower,_Phnom_Penh_(2020).jpg',
        'sofitel'    => 'https://commons.wikimedia.org/wiki/Special:FilePath/Sofitel_Phnom_Penh_Phokeethra_(2019).jpg',
        'raffles'    => 'https://commons.wikimedia.org/wiki/Special:FilePath/Raffles_Hotel_Le_Royal,_Phnom_Penh.jpg',
        'hyatt'      => 'https://commons.wikimedia.org/wiki/Special:FilePath/Hyatt_Regency_Phnom_Penh.jpg',
        'naga'       => 'https://commons.wikimedia.org/wiki/Special:FilePath/NagaWorld_Hotel_and_Entertainment_Complex.jpg',
        'chaktomuk'  => 'https://commons.wikimedia.org/wiki/Special:FilePath/Chaktomuk_Conference_Hall_Phnom_Penh.jpg',
        'diamond'    => 'https://commons.wikimedia.org/wiki/Special:FilePath/Koh_Pich_Diamond_Island_Phnom_Penh.jpg',
        'factory'    => 'https://commons.wikimedia.org/wiki/Special:FilePath/The_Factory_Phnom_Penh.jpg',
        'sokha'      => 'https://commons.wikimedia.org/wiki/Special:FilePath/Sokha_Phnom_Penh_Hotel.jpg',
        'intercont'  => 'https://commons.wikimedia.org/wiki/Special:FilePath/InterContinental_Phnom_Penh.jpg',
        'sokha_sr'   => 'https://commons.wikimedia.org/wiki/Special:FilePath/Sokha_Hotels_Siem_Reap.jpg',
        'angkor'     => 'https://commons.wikimedia.org/wiki/Special:FilePath/Angkor_Wat_sunrise.jpg',
    ];

    // All 25 provinces/cities of Cambodia
    public static array $provinces = [
        'Phnom Penh',
        'Siem Reap',
        'Sihanoukville (Preah Sihanouk)',
        'Battambang',
        'Kampong Cham',
        'Kampong Chhnang',
        'Kampong Speu',
        'Kampong Thom',
        'Kampot',
        'Kandal',
        'Kep',
        'Koh Kong',
        'Kratié',
        'Mondulkiri',
        'Oddar Meanchey',
        'Pailin',
        'Preah Vihear',
        'Prey Veng',
        'Pursat',
        'Ratanakiri',
        'Stung Treng',
        'Svay Rieng',
        'Takéo',
        'Tboung Khmum',
        'Banteay Meanchey',
    ];

    public function __construct()
    {
        $this->apiKey = env('ANTHROPIC_API_KEY', '');
    }

    public function generateSuggestions(array $eventData): array
    {
        if (empty($this->apiKey)) {
            return $this->fallbackSuggestions($eventData);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-opus-4-5',
                'max_tokens' => 2000,
                'messages'   => [['role' => 'user', 'content' => $this->buildPrompt($eventData)]],
            ]);

            if (!$response->successful()) {
                return $this->fallbackSuggestions($eventData);
            }

            $content = $response->json('content.0.text', '');
            $json    = $this->extractJson($content);

            if ($json) {
                foreach ($json['venues'] ?? [] as &$venue) {
                    $venue['image_url'] = $this->resolveVenueImage($venue['name'] ?? '');
                }
                return $json;
            }

            return $this->fallbackSuggestions($eventData);

        } catch (\Exception $e) {
            return $this->fallbackSuggestions($eventData);
        }
    }

    public function resolveVenueImage(string $name): string
    {
        $n = strtolower($name);
        if (str_contains($n, 'rosewood') || str_contains($n, 'vattanac'))   return $this->venueImages['rosewood'];
        if (str_contains($n, 'sofitel'))                                     return $this->venueImages['sofitel'];
        if (str_contains($n, 'raffles') || str_contains($n, 'le royal'))    return $this->venueImages['raffles'];
        if (str_contains($n, 'hyatt'))                                       return $this->venueImages['hyatt'];
        if (str_contains($n, 'naga'))                                        return $this->venueImages['naga'];
        if (str_contains($n, 'chaktomuk'))                                   return $this->venueImages['chaktomuk'];
        if (str_contains($n, 'diamond') || str_contains($n, 'koh pich'))    return $this->venueImages['diamond'];
        if (str_contains($n, 'factory'))                                     return $this->venueImages['factory'];
        if (str_contains($n, 'sokha') && str_contains($n, 'siem'))          return $this->venueImages['sokha_sr'];
        if (str_contains($n, 'sokha'))                                       return $this->venueImages['sokha'];
        if (str_contains($n, 'intercontinental'))                            return $this->venueImages['intercont'];
        if (str_contains($n, 'angkor') || str_contains($n, 'siem reap'))    return $this->venueImages['angkor'];
        return '';
    }

    private function buildPrompt(array $d): string
    {
        $type     = $d['event_type']  ?? 'event';
        $guests   = $d['guest_count'] ?? 100;
        $budget   = $d['budget']      ?? 5000;
        $style    = $d['style']       ?? 'modern';
        $venue    = $d['venue_pref']  ?? 'indoor';
        $meal     = $d['meal']        ?? 'buffet';
        $province = $d['province']    ?? 'Phnom Penh';

        return <<<PROMPT
You are an expert event planner in Cambodia. Return ONLY valid JSON, no markdown, no explanation.

Event details:
- Type: {$type}
- Location: {$province}, Cambodia
- Guests: {$guests}
- Budget: \${$budget} USD
- Style: {$style}
- Venue type: {$venue}
- Meal: {$meal}

Suggest 5 real, well-known event venues that actually exist in {$province}, Cambodia. If {$province} is a smaller province with fewer hotels, suggest the best available local venues such as provincial hotels, resort halls, community event spaces, or nearby venues. Always provide real place names with real addresses.

Also suggest 2 real local caterers and 2 real local decoration companies available in or near {$province}.

Return this exact JSON structure:
{"venues":[{"name":"","area":"","description":"","capacity_range":"","price_per_person":"","best_for":"","address":"","google_maps":"","phone":"","website":""}],"caterers":[{"name":"","specialty":"","price_range":"","contact":"","description":""}],"decor_companies":[{"name":"","specialty":"","price_range":"","contact":"","description":""}],"budget_breakdown":{"venue":40,"catering":35,"decoration":15,"photography":5,"other":5},"planning_tips":["","",""],"estimated_total":{"budget":{$budget},"venue_cost":0,"catering_cost":0,"decor_cost":0,"note":""}}
PROMPT;
    }

    private function extractJson(string $text): ?array
    {
        $decoded = json_decode($text, true);
        if ($decoded) return $decoded;
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if ($decoded) return $decoded;
        }
        return null;
    }

    private function fallbackSuggestions(array $d): array
    {
        $budget   = $d['budget']      ?? 5000;
        $guests   = $d['guest_count'] ?? 100;
        $province = $d['province']    ?? 'Phnom Penh';

        // Province-specific fallback venues
        $venues = $this->getFallbackVenues($province);

        return [
            'venues'           => $venues,
            'caterers'         => [
                ['name' => 'Malis Restaurant & Catering', 'specialty' => 'Khmer & International Cuisine',  'price_range' => '$18-45/person', 'contact' => '+855 23 221 022', 'description' => 'Award-winning Khmer cuisine — authentic Cambodian dishes with modern presentation.'],
                ['name' => 'Java Creative Catering',      'specialty' => 'Western & Fusion Buffet',        'price_range' => '$20-55/person', 'contact' => '+855 23 987 420', 'description' => 'Professional catering for corporate and private events.'],
            ],
            'decor_companies'  => [
                ['name' => 'Creative Event Cambodia',      'specialty' => 'Full venue theming & decoration', 'price_range' => '$800-5,000/event', 'contact' => '+855 12 345 678', 'description' => 'Full-service decoration covering flowers, lighting, backdrops, and complete venue transformation.'],
                ['name' => 'Beautiful Wedding Decoration', 'specialty' => 'Floral & wedding decor',          'price_range' => '$500-3,000/event', 'contact' => '+855 17 888 123', 'description' => 'Elegant floral arrangements and themed decorations.'],
            ],
            'budget_breakdown' => ['venue' => 40, 'catering' => 35, 'decoration' => 15, 'photography' => 5, 'other' => 5],
            'planning_tips'    => [
                "Book venues in {$province} at least 2-3 months in advance for popular dates.",
                'Rainy season (May-October) often offers 20-30% venue discounts.',
                'Always arrange backup power generator for events over 200 guests.',
            ],
            'estimated_total'  => [
                'budget'        => $budget,
                'venue_cost'    => (int)($budget * 0.40),
                'catering_cost' => (int)($budget * 0.35),
                'decor_cost'    => (int)($budget * 0.15),
                'note'          => "Based on {$guests} guests with \$" . number_format($budget) . " budget in {$province}",
            ],
        ];
    }

    private function getFallbackVenues(string $province): array
    {
        $siemReap = [
            ['name' => 'Sokha Angkor Resort',          'area' => 'Siem Reap City Centre',     'description' => 'Luxurious resort with grand ballroom and tropical gardens near Angkor Wat.',                   'capacity_range' => '100-800',  'price_per_person' => '$50-120',  'best_for' => 'Wedding, Gala, Conference',         'address' => 'National Road 6, Siem Reap',            'google_maps' => 'https://maps.google.com/?q=Sokha+Angkor+Resort',          'image_url' => $this->venueImages['sokha_sr'],  'phone' => '+855 63 969 999', 'website' => 'https://www.sokhahotels.com'],
            ['name' => 'Angkor Century Resort & Spa',  'area' => 'Airport Road, Siem Reap',   'description' => 'Colonial-style luxury resort with multiple event halls and lush garden spaces.',               'capacity_range' => '50-400',   'price_per_person' => '$40-90',   'best_for' => 'Wedding, Corporate Event',          'address' => 'Airport Road, Siem Reap',               'google_maps' => 'https://maps.google.com/?q=Angkor+Century+Resort',        'image_url' => '',              'phone' => '+855 63 963 777', 'website' => ''],
            ['name' => 'Raffles Grand Hotel d\'Angkor', 'area' => 'Vithei Charles de Gaulle',  'description' => 'Iconic 1932 colonial landmark — one of the most prestigious venues in Siem Reap.',            'capacity_range' => '50-300',   'price_per_person' => '$80-180',  'best_for' => 'Luxury Wedding, Heritage Event',    'address' => '1 Vithei Charles de Gaulle, Siem Reap', 'google_maps' => 'https://maps.google.com/?q=Raffles+Grand+Hotel+Angkor',   'image_url' => $this->venueImages['raffles'],   'phone' => '+855 63 963 888', 'website' => 'https://www.raffles.com/siem-reap'],
            ['name' => 'Amansara',                     'area' => 'Near Royal Palace, SR',     'description' => 'Exclusive boutique resort with intimate event spaces and UNESCO World Heritage views.',        'capacity_range' => '20-100',   'price_per_person' => '$150-350', 'best_for' => 'Intimate Wedding, VIP Gathering',  'address' => 'Road 60, Siem Reap',                    'google_maps' => 'https://maps.google.com/?q=Amansara+Siem+Reap',           'image_url' => '',              'phone' => '+855 63 760 333', 'website' => 'https://www.aman.com/resorts/amansara'],
            ['name' => 'Siem Reap Convention Centre',  'area' => 'Siem Reap City',            'description' => 'Modern convention facility suitable for large conferences, exhibitions, and public events.',   'capacity_range' => '200-2000', 'price_per_person' => '$15-40',   'best_for' => 'Conference, Exhibition, Public',    'address' => 'Siem Reap City',                        'google_maps' => 'https://maps.google.com/?q=Siem+Reap+Convention+Centre',  'image_url' => '',              'phone' => '+855 63 000 000', 'website' => ''],
        ];

        $sihanoukville = [
            ['name' => 'Sokha Beach Resort',           'area' => 'Sokha Beach, Sihanoukville', 'description' => 'Beachfront resort with stunning sea views and multiple event venues for all sizes.',          'capacity_range' => '100-600',  'price_per_person' => '$45-100',  'best_for' => 'Beach Wedding, Resort Event',       'address' => 'Sokha Beach, Sihanoukville',             'google_maps' => 'https://maps.google.com/?q=Sokha+Beach+Resort+Sihanoukville', 'image_url' => '', 'phone' => '+855 34 935 999', 'website' => 'https://www.sokhahotels.com'],
            ['name' => 'Independence Hotel',           'area' => 'Independence Beach',         'description' => 'Historic waterfront hotel with panoramic ocean views and classic event halls.',               'capacity_range' => '50-300',   'price_per_person' => '$40-90',   'best_for' => 'Wedding, Corporate Event',          'address' => 'Street 2 Thnou, Sihanoukville',         'google_maps' => 'https://maps.google.com/?q=Independence+Hotel+Sihanoukville', 'image_url' => '', 'phone' => '+855 34 934 300', 'website' => ''],
            ['name' => 'Serendipity Beach Resort',     'area' => 'Serendipity Beach',          'description' => 'Popular beachside resort known for outdoor events with stunning sunset backdrops.',           'capacity_range' => '50-250',   'price_per_person' => '$30-70',   'best_for' => 'Outdoor Event, Beach Party',        'address' => 'Serendipity Road, Sihanoukville',       'google_maps' => 'https://maps.google.com/?q=Serendipity+Beach+Resort',         'image_url' => '', 'phone' => '+855 34 933 730', 'website' => ''],
            ['name' => 'Managed Beach Resort',        'area' => 'Otres Beach',                 'description' => 'Relaxed beachfront setting ideal for casual gatherings and small private events.',            'capacity_range' => '30-150',   'price_per_person' => '$20-50',   'best_for' => 'Casual Gathering, Small Event',     'address' => 'Otres Beach, Sihanoukville',            'google_maps' => 'https://maps.google.com/?q=Otres+Beach+Sihanoukville',         'image_url' => '', 'phone' => '+855 34 000 000', 'website' => ''],
            ['name' => 'Pacific Hotel & Spa',          'area' => 'Sihanoukville City Centre',  'description' => 'Modern city hotel with function rooms suitable for corporate and social events.',             'capacity_range' => '50-300',   'price_per_person' => '$35-80',   'best_for' => 'Corporate, Social Event',           'address' => 'Ekareach Street, Sihanoukville',        'google_maps' => 'https://maps.google.com/?q=Pacific+Hotel+Sihanoukville',       'image_url' => '', 'phone' => '+855 34 933 033', 'website' => ''],
        ];

        $battambang = [
            ['name' => 'Sangker Villa Hotel',          'area' => 'Battambang City Centre',     'description' => 'Charming riverside hotel with garden event spaces and colonial architecture.',               'capacity_range' => '50-200',   'price_per_person' => '$25-60',   'best_for' => 'Wedding, Social Event',             'address' => 'Street 1.5, Battambang',                'google_maps' => 'https://maps.google.com/?q=Sangker+Villa+Hotel+Battambang', 'image_url' => '', 'phone' => '+855 53 953 895', 'website' => ''],
            ['name' => 'La Villa Hotel',               'area' => 'Riverside, Battambang',      'description' => 'Boutique heritage hotel with colonial charm, ideal for intimate celebrations.',              'capacity_range' => '30-150',   'price_per_person' => '$30-70',   'best_for' => 'Intimate Wedding, Private Event',   'address' => 'Riverside, Battambang',                 'google_maps' => 'https://maps.google.com/?q=La+Villa+Hotel+Battambang',      'image_url' => '', 'phone' => '+855 53 730 151', 'website' => ''],
            ['name' => 'Battambang Resort',            'area' => 'Battambang City',            'description' => 'Spacious resort with large event halls and outdoor areas for all types of events.',          'capacity_range' => '100-500',  'price_per_person' => '$20-50',   'best_for' => 'Conference, Wedding, Community',    'address' => 'National Road 5, Battambang',           'google_maps' => 'https://maps.google.com/?q=Battambang+Resort',              'image_url' => '', 'phone' => '+855 53 952 150', 'website' => ''],
            ['name' => 'Royal Hotel Battambang',       'area' => 'Battambang City Centre',     'description' => 'Classic hotel with function rooms suitable for local corporate and social events.',           'capacity_range' => '50-300',   'price_per_person' => '$18-45',   'best_for' => 'Corporate, Social Gathering',       'address' => 'Street 2, Battambang',                  'google_maps' => 'https://maps.google.com/?q=Royal+Hotel+Battambang',         'image_url' => '', 'phone' => '+855 53 952 529', 'website' => ''],
            ['name' => 'Battambang Convention Hall',   'area' => 'Battambang City',            'description' => 'Provincial convention centre for large-scale public and government events.',                  'capacity_range' => '200-1000', 'price_per_person' => '$10-25',   'best_for' => 'Conference, Exhibition, Ceremony',  'address' => 'Battambang City',                       'google_maps' => 'https://maps.google.com/?q=Battambang+Convention',          'image_url' => '', 'phone' => '+855 53 000 000', 'website' => ''],
        ];

        // Default Phnom Penh fallback
        $phnomPenh = [
            ['name' => 'Chaktomuk Conference Hall',              'area' => 'Riverside, Daun Penh',          'description' => 'Iconic riverside venue with Mekong views.',         'capacity_range' => '200-1500', 'price_per_person' => '$8-25',   'best_for' => 'Conference, Grand Opening', 'address' => 'Sisowath Quay, Daun Penh, Phnom Penh',     'google_maps' => 'https://maps.google.com/?q=Chaktomuk+Conference+Hall', 'image_url' => $this->venueImages['chaktomuk'], 'phone' => '+855 23 726 103', 'website' => ''],
            ['name' => 'Diamond Island Convention & Exhibition', 'area' => 'Koh Pich (Diamond Island)',      'description' => 'Modern large-scale venue on Diamond Island.',        'capacity_range' => '500-5000', 'price_per_person' => '$12-35',  'best_for' => 'Trade Show, Concert',       'address' => 'Koh Pich, Chamkarmon, Phnom Penh',         'google_maps' => 'https://maps.google.com/?q=Diamond+Island+Convention',  'image_url' => $this->venueImages['diamond'],   'phone' => '+855 23 220 000', 'website' => ''],
            ['name' => 'Sofitel Phnom Penh Phokeethra',         'area' => 'Tonle Bassac, Chamkarmon',       'description' => 'Five-star hotel with elegant ballrooms.',             'capacity_range' => '100-600',  'price_per_person' => '$55-120', 'best_for' => 'Wedding, Corporate Gala',   'address' => '26 Old August Blvd, Chamkarmon, Phnom Penh', 'google_maps' => 'https://maps.google.com/?q=Sofitel+Phnom+Penh',         'image_url' => $this->venueImages['sofitel'],   'phone' => '+855 23 999 200', 'website' => 'https://sofitel.accor.com'],
            ['name' => 'Rosewood Phnom Penh',                   'area' => 'BKK1, Vattanac Capital Tower',  'description' => 'Ultra-luxury hotel atop Vattanac Capital Tower.',    'capacity_range' => '50-400',   'price_per_person' => '$80-200', 'best_for' => 'Corporate Gala, VIP Launch', 'address' => 'Vattanac Capital Tower, BKK1',             'google_maps' => 'https://maps.google.com/?q=Rosewood+Phnom+Penh',        'image_url' => $this->venueImages['rosewood'],  'phone' => '+855 23 936 888', 'website' => 'https://www.rosewoodhotels.com'],
            ['name' => 'Raffles Hotel Le Royal',                 'area' => 'Daun Penh',                     'description' => 'Historic 1929 colonial landmark.',                  'capacity_range' => '50-300',   'price_per_person' => '$70-150', 'best_for' => 'Elegant Wedding, Heritage', 'address' => '92 Rukhak Vithei Daun Penh, Phnom Penh',   'google_maps' => 'https://maps.google.com/?q=Raffles+Hotel+Le+Royal',     'image_url' => $this->venueImages['raffles'],   'phone' => '+855 23 981 888', 'website' => 'https://www.raffles.com/phnom-penh'],
        ];

        return match (true) {
            str_contains(strtolower($province), 'siem reap')                     => $siemReap,
            str_contains(strtolower($province), 'sihanouk') ||
            str_contains(strtolower($province), 'sihanoukville')                 => $sihanoukville,
            str_contains(strtolower($province), 'battambang')                    => $battambang,
            default                                                               => $phnomPenh,
        };
    }
}
