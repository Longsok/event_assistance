<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiSuggestionService
{
    private string $apiKey;

    // Wikipedia Commons Special:FilePath — works in browsers, no API key needed
    // Falls back to gradient if file doesn't exist
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
        if (str_contains($n, 'sokha'))                                       return $this->venueImages['sokha'];
        if (str_contains($n, 'intercontinental'))                            return $this->venueImages['intercont'];
        return '';
    }

    private function buildPrompt(array $d): string
    {
        $type   = $d['event_type']  ?? 'event';
        $guests = $d['guest_count'] ?? 100;
        $budget = $d['budget']      ?? 5000;
        $style  = $d['style']       ?? 'modern';
        $venue  = $d['venue_pref']  ?? 'indoor';
        $meal   = $d['meal']        ?? 'buffet';

        return <<<PROMPT
You are an expert event planner in Phnom Penh, Cambodia. Return ONLY valid JSON, no markdown.

Event: {$type}, {$guests} guests, \${$budget} USD total budget, {$style} style, {$venue}, {$meal} meal.

Return JSON with 5 real Phnom Penh venues from this list ONLY: Rosewood Phnom Penh, Sofitel Phnom Penh Phokeethra, Hyatt Regency Phnom Penh, Raffles Hotel Le Royal, NagaWorld Hotel, Sokha Phnom Penh Hotel, InterContinental Phnom Penh, Chaktomuk Conference Hall, Diamond Island Convention Centre, Factory Phnom Penh. Pick the ones that best match the event type and budget.

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
        $budget = $d['budget']      ?? 5000;
        $guests = $d['guest_count'] ?? 100;

        return [
            'venues' => [
                ['name' => 'Chaktomuk Conference Hall',              'area' => 'Riverside, Daun Penh',          'description' => 'Iconic riverside venue with Mekong views. One of Phnom Penh\'s most recognizable event spaces for large public gatherings.',         'capacity_range' => '200-1500', 'price_per_person' => '$8-25',   'best_for' => 'Conference, Grand Opening, Public Events', 'address' => 'Sisowath Quay, Daun Penh, Phnom Penh',                    'google_maps' => 'https://maps.google.com/?q=Chaktomuk+Conference+Hall+Phnom+Penh',  'image_url' => $this->venueImages['chaktomuk'], 'phone' => '+855 23 726 103', 'website' => 'https://maps.app.goo.gl/chaktomuk'],
                ['name' => 'Diamond Island Convention & Exhibition', 'area' => 'Koh Pich (Diamond Island)',      'description' => 'Modern large-scale venue on Diamond Island with state-of-the-art facilities and flexible space for major events.',                 'capacity_range' => '500-5000', 'price_per_person' => '$12-35',  'best_for' => 'Trade Show, Concert, Grand Opening, Exhibition',    'address' => 'Koh Pich, Chamkarmon, Phnom Penh',                        'google_maps' => 'https://maps.google.com/?q=Diamond+Island+Convention+Phnom+Penh',  'image_url' => $this->venueImages['diamond'],   'phone' => '+855 23 220 000', 'website' => 'https://maps.app.goo.gl/diamond'],
                ['name' => 'Sofitel Phnom Penh Phokeethra',         'area' => 'Tonle Bassac, Chamkarmon',       'description' => 'Five-star hotel with elegant ballrooms and lush tropical gardens. World-class service with Cambodian elegance for premium events.',  'capacity_range' => '100-600',  'price_per_person' => '$55-120', 'best_for' => 'Wedding, Corporate Gala, Luxury Conference',        'address' => '26 Old August Blvd, Chamkarmon, Phnom Penh',              'google_maps' => 'https://maps.google.com/?q=Sofitel+Phnom+Penh+Phokeethra',         'image_url' => $this->venueImages['sofitel'],   'phone' => '+855 23 999 200', 'website' => 'https://sofitel.accor.com/gb/hotel-9077'],
                ['name' => 'Rosewood Phnom Penh',                   'area' => 'BKK1, Vattanac Capital Tower',  'description' => 'Ultra-luxury hotel atop the iconic Vattanac Capital Tower with panoramic views. The most prestigious address for elite events.',     'capacity_range' => '50-400',   'price_per_person' => '$80-200', 'best_for' => 'Corporate Gala, Luxury Wedding, VIP Launch',        'address' => 'Vattanac Capital Tower, Monivong Blvd, BKK1',             'google_maps' => 'https://maps.google.com/?q=Rosewood+Phnom+Penh+Vattanac',          'image_url' => $this->venueImages['rosewood'],  'phone' => '+855 23 936 888', 'website' => 'https://www.rosewoodhotels.com/en/phnom-penh'],
                ['name' => 'Raffles Hotel Le Royal',                 'area' => 'Daun Penh, near Central Market', 'description' => 'Historic 1929 colonial landmark with timeless elegance. Grand ballrooms and tropical gardens iconic for heritage events.',        'capacity_range' => '50-300',   'price_per_person' => '$70-150', 'best_for' => 'Elegant Wedding, Heritage Events, Corporate Dinner', 'address' => '92 Rukhak Vithei Daun Penh, Phnom Penh',                  'google_maps' => 'https://maps.google.com/?q=Raffles+Hotel+Le+Royal+Phnom+Penh',     'image_url' => $this->venueImages['raffles'],   'phone' => '+855 23 981 888', 'website' => 'https://www.raffles.com/phnom-penh'],
            ],
            'caterers' => [
                ['name' => 'Malis Restaurant & Catering', 'specialty' => 'Khmer & International Cuisine',  'price_range' => '$18-45/person', 'contact' => '+855 23 221 022', 'description' => 'Award-winning Khmer cuisine by Chef Luu Meng — authentic Cambodian dishes with modern presentation.'],
                ['name' => 'Java Creative Catering',      'specialty' => 'Western & Fusion Buffet',        'price_range' => '$20-55/person', 'contact' => '+855 23 987 420', 'description' => 'Professional catering for corporate and private events, known for quality and reliable service.'],
            ],
            'decor_companies' => [
                ['name' => 'Creative Event Cambodia',      'specialty' => 'Full venue theming & decoration', 'price_range' => '$800-5,000/event', 'contact' => '+855 12 345 678', 'description' => 'Full-service decoration covering flowers, lighting, backdrops, and complete venue transformation.'],
                ['name' => 'Beautiful Wedding Decoration', 'specialty' => 'Floral & wedding decor',          'price_range' => '$500-3,000/event', 'contact' => '+855 17 888 123', 'description' => 'Elegant floral arrangements and themed decorations specializing in weddings and celebrations.'],
            ],
            'budget_breakdown' => ['venue' => 40, 'catering' => 35, 'decoration' => 15, 'photography' => 5, 'other' => 5],
            'planning_tips' => [
                'Book venues 2-3 months ahead — peak season (November-January) fills fast.',
                'Rainy season (May-October) offers 20-30% venue discounts with similar quality.',
                'Always arrange backup power for events over 200 guests.',
            ],
            'estimated_total' => [
                'budget'        => $budget,
                'venue_cost'    => (int)($budget * 0.40),
                'catering_cost' => (int)($budget * 0.35),
                'decor_cost'    => (int)($budget * 0.15),
                'note'          => "Based on {$guests} guests with \$" . number_format($budget) . " budget",
            ],
        ];
    }
}
