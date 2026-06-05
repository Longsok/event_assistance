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
        'Kratie',
        'Mondulkiri',
        'Oddar Meanchey',
        'Pailin',
        'Preah Vihear',
        'Prey Veng',
        'Pursat',
        'Ratanakiri',
        'Stung Treng',
        'Svay Rieng',
        'Takeo',
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

        $venues = $this->getFallbackVenues($province);

        return [
            'venues'           => $venues,
            'caterers'         => [
                ['name' => 'Malis Restaurant & Catering', 'specialty' => 'Khmer & International Cuisine',  'price_range' => '$18-45/person', 'contact' => '+855 23 221 022', 'description' => 'Award-winning Khmer cuisine with authentic Cambodian dishes and modern presentation.'],
                ['name' => 'Java Creative Catering',      'specialty' => 'Western & Fusion Buffet',        'price_range' => '$20-55/person', 'contact' => '+855 23 987 420', 'description' => 'Professional catering for corporate and private events across Cambodia.'],
            ],
            'decor_companies'  => [
                ['name' => 'Creative Event Cambodia',      'specialty' => 'Full venue theming & decoration', 'price_range' => '$800-5,000/event', 'contact' => '+855 12 345 678', 'description' => 'Full-service decoration covering flowers, lighting, backdrops, and complete venue transformation.'],
                ['name' => 'Beautiful Wedding Decoration', 'specialty' => 'Floral & wedding decor',          'price_range' => '$500-3,000/event', 'contact' => '+855 17 888 123', 'description' => 'Elegant floral arrangements and themed decorations for all event types.'],
            ],
            'budget_breakdown' => ['venue' => 40, 'catering' => 35, 'decoration' => 15, 'photography' => 5, 'other' => 5],
            'planning_tips'    => [
                "Book venues in {$province} at least 2-3 months in advance for popular dates.",
                'Rainy season (May-October) often offers 20-30% venue discounts.',
                'Always arrange a backup power generator for events over 200 guests.',
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
        $p = strtolower($province);

        // ── PHNOM PENH ──────────────────────────────────────────────────────
        $phnomPenh = [
            ['name'=>'Chaktomuk Conference Hall','area'=>'Riverside, Daun Penh','description'=>'Iconic riverside venue overlooking the Mekong and Tonle Sap confluence.','capacity_range'=>'200-1500','price_per_person'=>'$8-25','best_for'=>'Conference, Grand Opening, Gala','address'=>'Sisowath Quay, Daun Penh, Phnom Penh','google_maps'=>'https://maps.google.com/?q=Chaktomuk+Conference+Hall','image_url'=>$this->venueImages['chaktomuk'],'phone'=>'+855 23 726 103','website'=>''],
            ['name'=>'Diamond Island Convention & Exhibition','area'=>'Koh Pich (Diamond Island)','description'=>'Modern large-scale convention centre on Diamond Island, ideal for major events.','capacity_range'=>'500-5000','price_per_person'=>'$12-35','best_for'=>'Trade Show, Concert, Exhibition','address'=>'Koh Pich, Chamkarmon, Phnom Penh','google_maps'=>'https://maps.google.com/?q=Diamond+Island+Convention','image_url'=>$this->venueImages['diamond'],'phone'=>'+855 23 220 000','website'=>''],
            ['name'=>'Sofitel Phnom Penh Phokeethra','area'=>'Tonle Bassac, Chamkarmon','description'=>'Five-star hotel featuring elegant ballrooms and world-class event services.','capacity_range'=>'100-600','price_per_person'=>'$55-120','best_for'=>'Wedding, Corporate Gala, Banquet','address'=>'26 Old August Blvd, Chamkarmon, Phnom Penh','google_maps'=>'https://maps.google.com/?q=Sofitel+Phnom+Penh','image_url'=>$this->venueImages['sofitel'],'phone'=>'+855 23 999 200','website'=>'https://sofitel.accor.com'],
            ['name'=>'Rosewood Phnom Penh','area'=>'BKK1, Vattanac Capital Tower','description'=>'Ultra-luxury hotel on the top floors of Vattanac Capital Tower with panoramic views.','capacity_range'=>'50-400','price_per_person'=>'$80-200','best_for'=>'Corporate Gala, VIP Launch, Luxury Wedding','address'=>'Vattanac Capital Tower, BKK1, Phnom Penh','google_maps'=>'https://maps.google.com/?q=Rosewood+Phnom+Penh','image_url'=>$this->venueImages['rosewood'],'phone'=>'+855 23 936 888','website'=>'https://www.rosewoodhotels.com'],
            ['name'=>'Raffles Hotel Le Royal','area'=>'Daun Penh','description'=>'Historic 1929 colonial landmark — the most iconic luxury hotel in Phnom Penh.','capacity_range'=>'50-300','price_per_person'=>'$70-150','best_for'=>'Elegant Wedding, Heritage Event, VIP Dinner','address'=>'92 Rukhak Vithei Daun Penh, Phnom Penh','google_maps'=>'https://maps.google.com/?q=Raffles+Hotel+Le+Royal','image_url'=>$this->venueImages['raffles'],'phone'=>'+855 23 981 888','website'=>'https://www.raffles.com/phnom-penh'],
        ];

        // ── SIEM REAP ────────────────────────────────────────────────────────
        $siemReap = [
            ['name'=>'Sokha Angkor Resort','area'=>'Siem Reap City Centre','description'=>'Luxurious resort with grand ballroom and tropical gardens near Angkor Wat.','capacity_range'=>'100-800','price_per_person'=>'$50-120','best_for'=>'Wedding, Gala, Conference','address'=>'National Road 6, Siem Reap','google_maps'=>'https://maps.google.com/?q=Sokha+Angkor+Resort','image_url'=>$this->venueImages['sokha_sr'],'phone'=>'+855 63 969 999','website'=>'https://www.sokhahotels.com'],
            ['name'=>'Angkor Century Resort & Spa','area'=>'Airport Road, Siem Reap','description'=>'Colonial-style luxury resort with multiple event halls and lush garden spaces.','capacity_range'=>'50-400','price_per_person'=>'$40-90','best_for'=>'Wedding, Corporate Event','address'=>'Airport Road, Siem Reap','google_maps'=>'https://maps.google.com/?q=Angkor+Century+Resort','image_url'=>'','phone'=>'+855 63 963 777','website'=>''],
            ['name'=>"Raffles Grand Hotel d'Angkor",'area'=>'Vithei Charles de Gaulle, Siem Reap','description'=>'Iconic 1932 colonial landmark — one of the most prestigious venues in Siem Reap.','capacity_range'=>'50-300','price_per_person'=>'$80-180','best_for'=>'Luxury Wedding, Heritage Event','address'=>'1 Vithei Charles de Gaulle, Siem Reap','google_maps'=>'https://maps.google.com/?q=Raffles+Grand+Hotel+Angkor','image_url'=>$this->venueImages['raffles'],'phone'=>'+855 63 963 888','website'=>'https://www.raffles.com/siem-reap'],
            ['name'=>'Amansara','area'=>'Near Royal Palace, Siem Reap','description'=>'Exclusive boutique resort with intimate event spaces and UNESCO World Heritage views.','capacity_range'=>'20-100','price_per_person'=>'$150-350','best_for'=>'Intimate Wedding, VIP Gathering','address'=>'Road 60, Siem Reap','google_maps'=>'https://maps.google.com/?q=Amansara+Siem+Reap','image_url'=>'','phone'=>'+855 63 760 333','website'=>'https://www.aman.com/resorts/amansara'],
            ['name'=>'Siem Reap Convention Centre','area'=>'Siem Reap City','description'=>'Modern convention facility for large conferences, exhibitions, and public events.','capacity_range'=>'200-2000','price_per_person'=>'$15-40','best_for'=>'Conference, Exhibition, Public Event','address'=>'Siem Reap City','google_maps'=>'https://maps.google.com/?q=Siem+Reap+Convention+Centre','image_url'=>'','phone'=>'+855 63 000 000','website'=>''],
        ];

        // ── SIHANOUKVILLE ────────────────────────────────────────────────────
        $sihanoukville = [
            ['name'=>'Sokha Beach Resort','area'=>'Sokha Beach, Sihanoukville','description'=>'Beachfront resort with stunning sea views and multiple event venues.','capacity_range'=>'100-600','price_per_person'=>'$45-100','best_for'=>'Beach Wedding, Resort Event','address'=>'Sokha Beach, Sihanoukville','google_maps'=>'https://maps.google.com/?q=Sokha+Beach+Resort+Sihanoukville','image_url'=>'','phone'=>'+855 34 935 999','website'=>'https://www.sokhahotels.com'],
            ['name'=>'Independence Hotel','area'=>'Independence Beach, Sihanoukville','description'=>'Historic waterfront hotel with panoramic ocean views and classic event halls.','capacity_range'=>'50-300','price_per_person'=>'$40-90','best_for'=>'Wedding, Corporate Event','address'=>'Street 2 Thnou, Sihanoukville','google_maps'=>'https://maps.google.com/?q=Independence+Hotel+Sihanoukville','image_url'=>'','phone'=>'+855 34 934 300','website'=>''],
            ['name'=>'Serendipity Beach Resort','area'=>'Serendipity Beach, Sihanoukville','description'=>'Popular beachside resort known for outdoor events with stunning sunset backdrops.','capacity_range'=>'50-250','price_per_person'=>'$30-70','best_for'=>'Outdoor Event, Beach Party','address'=>'Serendipity Road, Sihanoukville','google_maps'=>'https://maps.google.com/?q=Serendipity+Beach+Resort','image_url'=>'','phone'=>'+855 34 933 730','website'=>''],
            ['name'=>'Pacific Hotel & Spa','area'=>'Sihanoukville City Centre','description'=>'Modern city hotel with function rooms for corporate and social events.','capacity_range'=>'50-300','price_per_person'=>'$35-80','best_for'=>'Corporate, Social Event','address'=>'Ekareach Street, Sihanoukville','google_maps'=>'https://maps.google.com/?q=Pacific+Hotel+Sihanoukville','image_url'=>'','phone'=>'+855 34 933 033','website'=>''],
            ['name'=>'Otres Beach Resort','area'=>'Otres Beach, Sihanoukville','description'=>'Relaxed beachfront setting ideal for casual gatherings and private events.','capacity_range'=>'30-150','price_per_person'=>'$20-50','best_for'=>'Casual Gathering, Small Event','address'=>'Otres Beach, Sihanoukville','google_maps'=>'https://maps.google.com/?q=Otres+Beach+Sihanoukville','image_url'=>'','phone'=>'+855 34 000 000','website'=>''],
        ];

        // ── BATTAMBANG ───────────────────────────────────────────────────────
        $battambang = [
            ['name'=>'Sangker Villa Hotel','area'=>'Battambang City Centre','description'=>'Charming riverside hotel with garden event spaces and colonial architecture.','capacity_range'=>'50-200','price_per_person'=>'$25-60','best_for'=>'Wedding, Social Event','address'=>'Street 1.5, Battambang','google_maps'=>'https://maps.google.com/?q=Sangker+Villa+Hotel+Battambang','image_url'=>'','phone'=>'+855 53 953 895','website'=>''],
            ['name'=>'La Villa Hotel','area'=>'Riverside, Battambang','description'=>'Boutique heritage hotel with colonial charm, ideal for intimate celebrations.','capacity_range'=>'30-150','price_per_person'=>'$30-70','best_for'=>'Intimate Wedding, Private Event','address'=>'Riverside, Battambang','google_maps'=>'https://maps.google.com/?q=La+Villa+Hotel+Battambang','image_url'=>'','phone'=>'+855 53 730 151','website'=>''],
            ['name'=>'Battambang Resort','area'=>'Battambang City','description'=>'Spacious resort with large event halls and outdoor areas for all event types.','capacity_range'=>'100-500','price_per_person'=>'$20-50','best_for'=>'Conference, Wedding, Community Event','address'=>'National Road 5, Battambang','google_maps'=>'https://maps.google.com/?q=Battambang+Resort','image_url'=>'','phone'=>'+855 53 952 150','website'=>''],
            ['name'=>'Royal Hotel Battambang','area'=>'Battambang City Centre','description'=>'Classic hotel with function rooms for local corporate and social events.','capacity_range'=>'50-300','price_per_person'=>'$18-45','best_for'=>'Corporate, Social Gathering','address'=>'Street 2, Battambang','google_maps'=>'https://maps.google.com/?q=Royal+Hotel+Battambang','image_url'=>'','phone'=>'+855 53 952 529','website'=>''],
            ['name'=>'Battambang Convention Hall','area'=>'Battambang City','description'=>'Provincial convention centre for large-scale public and government events.','capacity_range'=>'200-1000','price_per_person'=>'$10-25','best_for'=>'Conference, Exhibition, Ceremony','address'=>'Battambang City','google_maps'=>'https://maps.google.com/?q=Battambang+Convention','image_url'=>'','phone'=>'+855 53 000 000','website'=>''],
        ];

        // ── KAMPONG CHAM ─────────────────────────────────────────────────────
        $kampongCham = [
            ['name'=>'Mekong Hotel Kampong Cham','area'=>'Riverside, Kampong Cham City','description'=>'Riverside hotel with scenic Mekong views and comfortable event facilities.','capacity_range'=>'50-250','price_per_person'=>'$15-40','best_for'=>'Wedding, Social Gathering, Ceremony','address'=>'Riverside Road, Kampong Cham','google_maps'=>'https://maps.google.com/?q=Mekong+Hotel+Kampong+Cham','image_url'=>'','phone'=>'+855 42 941 536','website'=>''],
            ['name'=>'Kampong Cham Provincial Hotel','area'=>'Kampong Cham City Centre','description'=>'Main provincial hotel with banquet halls suitable for all types of celebrations.','capacity_range'=>'50-300','price_per_person'=>'$12-35','best_for'=>'Wedding, Ceremony, Conference','address'=>'Kampong Cham City','google_maps'=>'https://maps.google.com/?q=Kampong+Cham+Provincial+Hotel','image_url'=>'','phone'=>'+855 42 941 000','website'=>''],
            ['name'=>'Lan Komar Hotel','area'=>'Kampong Cham City','description'=>'Local hotel with banquet rooms popular for weddings and traditional ceremonies.','capacity_range'=>'50-200','price_per_person'=>'$10-28','best_for'=>'Wedding, Baby Blessing, Birthday','address'=>'National Road 7, Kampong Cham','google_maps'=>'https://maps.google.com/?q=Lan+Komar+Hotel+Kampong+Cham','image_url'=>'','phone'=>'+855 42 941 888','website'=>''],
            ['name'=>'Kampong Cham Convention Hall','area'=>'Kampong Cham City Centre','description'=>'Provincial convention centre for large community and government events.','capacity_range'=>'200-800','price_per_person'=>'$8-20','best_for'=>'Conference, Public Event, Ceremony','address'=>'Kampong Cham City','google_maps'=>'https://maps.google.com/?q=Kampong+Cham+Convention','image_url'=>'','phone'=>'+855 42 000 002','website'=>''],
            ['name'=>'Kampong Cham Garden Resort','area'=>'Outskirts of Kampong Cham','description'=>'Garden resort with outdoor event spaces ideal for traditional Khmer ceremonies.','capacity_range'=>'100-400','price_per_person'=>'$12-30','best_for'=>'Traditional Ceremony, Outdoor Wedding','address'=>'Kampong Cham Province','google_maps'=>'https://maps.google.com/?q=Kampong+Cham+Garden+Resort','image_url'=>'','phone'=>'+855 42 000 004','website'=>''],
        ];

        // ── KAMPONG CHHNANG ──────────────────────────────────────────────────
        $kampongChhnang = [
            ['name'=>'Kampong Chhnang Provincial Guesthouse','area'=>'Kampong Chhnang City','description'=>'Main government guesthouse with basic event facilities for local ceremonies.','capacity_range'=>'50-200','price_per_person'=>'$8-20','best_for'=>'Ceremony, Community Event','address'=>'Kampong Chhnang City','google_maps'=>'https://maps.google.com/?q=Kampong+Chhnang+Guesthouse','image_url'=>'','phone'=>'+855 26 000 001','website'=>''],
            ['name'=>'Tonle Sap Riverside Venue','area'=>'Tonle Sap Riverbank, Kampong Chhnang','description'=>'Scenic riverbank venue overlooking the Tonle Sap Lake, perfect for outdoor gatherings.','capacity_range'=>'50-300','price_per_person'=>'$10-25','best_for'=>'Outdoor Event, Social Gathering','address'=>'Riverside, Kampong Chhnang','google_maps'=>'https://maps.google.com/?q=Kampong+Chhnang+Riverside','image_url'=>'','phone'=>'+855 26 000 002','website'=>''],
            ['name'=>'Kampong Chhnang Hotel','area'=>'Kampong Chhnang City Centre','description'=>'Local hotel offering banquet rooms for weddings and social celebrations.','capacity_range'=>'50-150','price_per_person'=>'$10-25','best_for'=>'Wedding, Birthday, Social Event','address'=>'Kampong Chhnang City','google_maps'=>'https://maps.google.com/?q=Kampong+Chhnang+Hotel','image_url'=>'','phone'=>'+855 26 000 003','website'=>''],
            ['name'=>'Kampong Chhnang Community Hall','area'=>'Kampong Chhnang City','description'=>'Community hall for medium-sized local events, conferences, and ceremonies.','capacity_range'=>'100-400','price_per_person'=>'$6-18','best_for'=>'Community Event, Conference','address'=>'Kampong Chhnang City','google_maps'=>'https://maps.google.com/?q=Kampong+Chhnang+Community+Hall','image_url'=>'','phone'=>'+855 26 000 004','website'=>''],
            ['name'=>'Phum Thmei Resort','area'=>'Kampong Chhnang Province','description'=>'Rural resort with garden spaces ideal for intimate celebrations and weekend events.','capacity_range'=>'30-120','price_per_person'=>'$12-28','best_for'=>'Intimate Wedding, Retreat','address'=>'Kampong Chhnang Province','google_maps'=>'https://maps.google.com/?q=Phum+Thmei+Resort+Kampong+Chhnang','image_url'=>'','phone'=>'+855 26 000 005','website'=>''],
        ];

        // ── KAMPONG SPEU ─────────────────────────────────────────────────────
        $kampongSpeu = [
            ['name'=>'Kampong Speu Provincial Hotel','area'=>'Chbar Mon, Kampong Speu','description'=>'Main provincial hotel in Chbar Mon with banquet and function facilities.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Conference, Ceremony','address'=>'Chbar Mon, Kampong Speu','google_maps'=>'https://maps.google.com/?q=Kampong+Speu+Provincial+Hotel','image_url'=>'','phone'=>'+855 25 000 001','website'=>''],
            ['name'=>'Aural Mountain Resort','area'=>'Aural District, Kampong Speu','description'=>'Mountain resort near Phnom Aural offering scenic outdoor event spaces.','capacity_range'=>'30-150','price_per_person'=>'$15-35','best_for'=>'Outdoor Event, Retreat, Team Building','address'=>'Aural District, Kampong Speu','google_maps'=>'https://maps.google.com/?q=Aural+Mountain+Resort+Kampong+Speu','image_url'=>'','phone'=>'+855 25 000 002','website'=>''],
            ['name'=>'Kampong Speu Convention Hall','area'=>'Chbar Mon City','description'=>'Provincial convention centre suitable for government and community events.','capacity_range'=>'100-500','price_per_person'=>'$8-20','best_for'=>'Conference, Public Event','address'=>'Chbar Mon, Kampong Speu','google_maps'=>'https://maps.google.com/?q=Kampong+Speu+Convention','image_url'=>'','phone'=>'+855 25 000 003','website'=>''],
            ['name'=>'Sugar Palm Garden Venue','area'=>'Kampong Speu Province','description'=>'Garden event venue surrounded by sugar palm trees, ideal for traditional ceremonies.','capacity_range'=>'50-300','price_per_person'=>'$10-25','best_for'=>'Traditional Wedding, Ceremony','address'=>'Kampong Speu Province','google_maps'=>'https://maps.google.com/?q=Sugar+Palm+Garden+Kampong+Speu','image_url'=>'','phone'=>'+855 25 000 004','website'=>''],
            ['name'=>'Chbar Mon Grand Hall','area'=>'Chbar Mon, Kampong Speu','description'=>'Grand hall in the provincial capital for large weddings and social celebrations.','capacity_range'=>'100-600','price_per_person'=>'$10-28','best_for'=>'Wedding, Grand Ceremony','address'=>'Chbar Mon City, Kampong Speu','google_maps'=>'https://maps.google.com/?q=Chbar+Mon+Grand+Hall','image_url'=>'','phone'=>'+855 25 000 005','website'=>''],
        ];

        // ── KAMPONG THOM ─────────────────────────────────────────────────────
        $kampongThom = [
            ['name'=>'Kampong Thom Provincial Hotel','area'=>'Kampong Thom City','description'=>'Main provincial hotel with banquet hall for weddings and social events.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Social Event, Conference','address'=>'Kampong Thom City','google_maps'=>'https://maps.google.com/?q=Kampong+Thom+Hotel','image_url'=>'','phone'=>'+855 62 961 395','website'=>''],
            ['name'=>'Sambor Prei Kuk Resort','area'=>'Near Sambor Prei Kuk Temple','description'=>'Resort near the UNESCO-listed Sambor Prei Kuk temples, ideal for heritage-themed events.','capacity_range'=>'30-150','price_per_person'=>'$15-40','best_for'=>'Heritage Event, Retreat','address'=>'Sambor Village, Kampong Thom','google_maps'=>'https://maps.google.com/?q=Sambor+Prei+Kuk+Resort','image_url'=>'','phone'=>'+855 62 000 002','website'=>''],
            ['name'=>'Stung Sen Riverside Hotel','area'=>'Stung Sen River, Kampong Thom','description'=>'Riverside hotel overlooking the Stung Sen River with garden event areas.','capacity_range'=>'50-200','price_per_person'=>'$12-30','best_for'=>'Wedding, Social Gathering','address'=>'Stung Sen Riverside, Kampong Thom','google_maps'=>'https://maps.google.com/?q=Stung+Sen+Hotel+Kampong+Thom','image_url'=>'','phone'=>'+855 62 000 003','website'=>''],
            ['name'=>'Kampong Thom Convention Hall','area'=>'Kampong Thom City','description'=>'Provincial hall for large community events, conferences, and government functions.','capacity_range'=>'100-500','price_per_person'=>'$8-20','best_for'=>'Conference, Community Event','address'=>'Kampong Thom City','google_maps'=>'https://maps.google.com/?q=Kampong+Thom+Convention','image_url'=>'','phone'=>'+855 62 000 004','website'=>''],
            ['name'=>'Tonle Sap Eco Lodge','area'=>'Tonle Sap Floodplain, Kampong Thom','description'=>'Eco lodge on the Tonle Sap floodplain offering unique outdoor event experiences.','capacity_range'=>'20-100','price_per_person'=>'$20-45','best_for'=>'Eco Event, Intimate Gathering','address'=>'Tonle Sap Floodplain, Kampong Thom','google_maps'=>'https://maps.google.com/?q=Tonle+Sap+Eco+Lodge+Kampong+Thom','image_url'=>'','phone'=>'+855 62 000 005','website'=>''],
        ];

        // ── KAMPOT ───────────────────────────────────────────────────────────
        $kampot = [
            ['name'=>'Kampot Traditional House','area'=>'Kampot Town Centre','description'=>'Beautifully restored colonial house with garden — one of the most popular event venues in Kampot.','capacity_range'=>'30-150','price_per_person'=>'$20-50','best_for'=>'Intimate Wedding, Private Event','address'=>'Kampot Town','google_maps'=>'https://maps.google.com/?q=Kampot+Traditional+House','image_url'=>'','phone'=>'+855 33 000 001','website'=>''],
            ['name'=>'Bokor Palace Hotel (Restored)','area'=>'Bokor Hill Station, Kampot','description'=>'Iconic colonial building on Bokor Hill with breathtaking views over the Gulf of Thailand.','capacity_range'=>'50-200','price_per_person'=>'$25-60','best_for'=>'Heritage Event, Gala, Wedding','address'=>'Bokor Hill Station, Kampot','google_maps'=>'https://maps.google.com/?q=Bokor+Palace+Hotel+Kampot','image_url'=>'','phone'=>'+855 33 000 002','website'=>''],
            ['name'=>'Kampot River Garden','area'=>'Kampot Riverside','description'=>'Lush riverside garden venue perfect for outdoor weddings and celebrations.','capacity_range'=>'50-250','price_per_person'=>'$18-45','best_for'=>'Outdoor Wedding, Garden Party','address'=>'Riverside Road, Kampot','google_maps'=>'https://maps.google.com/?q=Kampot+River+Garden','image_url'=>'','phone'=>'+855 33 000 003','website'=>''],
            ['name'=>'Kampot Hotel & Convention','area'=>'Kampot Town','description'=>'Modern hotel with convention facilities for corporate and social events.','capacity_range'=>'50-300','price_per_person'=>'$15-38','best_for'=>'Conference, Wedding, Social Event','address'=>'Kampot Town','google_maps'=>'https://maps.google.com/?q=Kampot+Hotel+Convention','image_url'=>'','phone'=>'+855 33 000 004','website'=>''],
            ['name'=>'Pepper Farm Event Space','area'=>'Kampot Province','description'=>'Authentic pepper farm setting — unique venue for events celebrating Cambodian heritage.','capacity_range'=>'30-120','price_per_person'=>'$20-40','best_for'=>'Cultural Event, Private Gathering','address'=>'Kampot Province','google_maps'=>'https://maps.google.com/?q=Kampot+Pepper+Farm','image_url'=>'','phone'=>'+855 33 000 005','website'=>''],
        ];

        // ── KANDAL ───────────────────────────────────────────────────────────
        $kandal = [
            ['name'=>'Kandal Provincial Hall','area'=>'Ta Khmau, Kandal','description'=>'Main provincial hall in Ta Khmau city for large ceremonies and community events.','capacity_range'=>'100-600','price_per_person'=>'$8-22','best_for'=>'Conference, Ceremony, Public Event','address'=>'Ta Khmau, Kandal Province','google_maps'=>'https://maps.google.com/?q=Kandal+Provincial+Hall+Ta+Khmau','image_url'=>'','phone'=>'+855 24 000 001','website'=>''],
            ['name'=>'Mekong Riverside Resort Kandal','area'=>'Mekong Riverside, Kandal','description'=>'Riverside resort with garden event spaces along the Mekong River.','capacity_range'=>'50-300','price_per_person'=>'$15-40','best_for'=>'Wedding, Social Gathering','address'=>'Mekong Riverside, Kandal Province','google_maps'=>'https://maps.google.com/?q=Mekong+Riverside+Resort+Kandal','image_url'=>'','phone'=>'+855 24 000 002','website'=>''],
            ['name'=>'Ta Khmau Grand Hotel','area'=>'Ta Khmau City, Kandal','description'=>'Modern hotel near Phnom Penh with full event and banquet facilities.','capacity_range'=>'50-400','price_per_person'=>'$18-45','best_for'=>'Wedding, Corporate Event','address'=>'Ta Khmau, Kandal','google_maps'=>'https://maps.google.com/?q=Ta+Khmau+Grand+Hotel','image_url'=>'','phone'=>'+855 24 000 003','website'=>''],
            ['name'=>'Kandal Silk Island Resort','area'=>'Koh Dach (Silk Island), Kandal','description'=>'Charming resort on Koh Dach island with traditional Khmer architecture and garden events.','capacity_range'=>'30-150','price_per_person'=>'$20-45','best_for'=>'Cultural Event, Intimate Wedding','address'=>'Koh Dach, Kandal Province','google_maps'=>'https://maps.google.com/?q=Silk+Island+Resort+Kandal','image_url'=>'','phone'=>'+855 24 000 004','website'=>''],
            ['name'=>'Phnom Penh Outskirts Garden Venue','area'=>'Kandal Province (near Phnom Penh)','description'=>'Spacious garden venue just outside Phnom Penh — popular for large traditional weddings.','capacity_range'=>'200-1000','price_per_person'=>'$10-25','best_for'=>'Large Wedding, Traditional Ceremony','address'=>'Kandal Province','google_maps'=>'https://maps.google.com/?q=Garden+Wedding+Venue+Kandal','image_url'=>'','phone'=>'+855 24 000 005','website'=>''],
        ];

        // ── KEP ──────────────────────────────────────────────────────────────
        $kep = [
            ['name'=>'Kep Beach Club','area'=>'Kep Beach','description'=>'Beachside venue with stunning Gulf of Thailand views, ideal for intimate celebrations.','capacity_range'=>'20-100','price_per_person'=>'$25-60','best_for'=>'Beach Wedding, Private Party','address'=>'Kep Beach, Kep Province','google_maps'=>'https://maps.google.com/?q=Kep+Beach+Club','image_url'=>'','phone'=>'+855 36 000 001','website'=>''],
            ['name'=>'Knai Bang Chatt Resort','area'=>'Kep Seafront','description'=>'Award-winning boutique resort on the Kep seafront with exclusive event spaces.','capacity_range'=>'20-80','price_per_person'=>'$60-150','best_for'=>'Luxury Wedding, VIP Event','address'=>'Kep Seafront, Kep Province','google_maps'=>'https://maps.google.com/?q=Knai+Bang+Chatt+Kep','image_url'=>'','phone'=>'+855 36 210 650','website'=>'https://www.knaibangchatt.com'],
            ['name'=>'Kep Provincial Hotel','area'=>'Kep Town','description'=>'Main provincial hotel with basic event facilities for local gatherings.','capacity_range'=>'30-150','price_per_person'=>'$15-35','best_for'=>'Social Gathering, Ceremony','address'=>'Kep Town, Kep Province','google_maps'=>'https://maps.google.com/?q=Kep+Provincial+Hotel','image_url'=>'','phone'=>'+855 36 000 003','website'=>''],
            ['name'=>'Veranda Natural Resort','area'=>'Kep Hills','description'=>'Hilltop resort with sea views and intimate garden terraces for small events.','capacity_range'=>'20-80','price_per_person'=>'$40-90','best_for'=>'Intimate Wedding, Retreat','address'=>'Kep Hills, Kep Province','google_maps'=>'https://maps.google.com/?q=Veranda+Natural+Resort+Kep','image_url'=>'','phone'=>'+855 36 000 004','website'=>''],
            ['name'=>'Kep Crab Market Waterfront','area'=>'Kep Crab Market','description'=>'Unique waterfront venue at the famous Kep crab market — ideal for seafood-themed events.','capacity_range'=>'30-120','price_per_person'=>'$15-40','best_for'=>'Cultural Event, Private Gathering','address'=>'Kep Crab Market, Kep Province','google_maps'=>'https://maps.google.com/?q=Kep+Crab+Market','image_url'=>'','phone'=>'+855 36 000 005','website'=>''],
        ];

        // ── KOH KONG ─────────────────────────────────────────────────────────
        $kohKong = [
            ['name'=>'Koh Kong City Hotel','area'=>'Koh Kong City','description'=>'Main hotel in Koh Kong City with function facilities for events and celebrations.','capacity_range'=>'50-200','price_per_person'=>'$15-35','best_for'=>'Wedding, Social Event','address'=>'Koh Kong City','google_maps'=>'https://maps.google.com/?q=Koh+Kong+City+Hotel','image_url'=>'','phone'=>'+855 35 936 567','website'=>''],
            ['name'=>'Cardamom Tented Camp','area'=>'Cardamom Mountains, Koh Kong','description'=>'Eco-resort in the Cardamom Mountains — unique venue for nature-themed events.','capacity_range'=>'20-80','price_per_person'=>'$40-100','best_for'=>'Eco Event, Team Building, Retreat','address'=>'Cardamom Mountains, Koh Kong','google_maps'=>'https://maps.google.com/?q=Cardamom+Tented+Camp+Koh+Kong','image_url'=>'','phone'=>'+855 35 000 002','website'=>''],
            ['name'=>'Tatai Riverfront Resort','area'=>'Tatai River, Koh Kong','description'=>'Riverside resort on the Tatai River with outdoor event spaces and jungle views.','capacity_range'=>'30-150','price_per_person'=>'$25-60','best_for'=>'Outdoor Wedding, Private Event','address'=>'Tatai River, Koh Kong Province','google_maps'=>'https://maps.google.com/?q=Tatai+River+Resort+Koh+Kong','image_url'=>'','phone'=>'+855 35 000 003','website'=>''],
            ['name'=>'Koh Kong Convention Hall','area'=>'Koh Kong City','description'=>'Provincial hall for community events, conferences, and official ceremonies.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony','address'=>'Koh Kong City','google_maps'=>'https://maps.google.com/?q=Koh+Kong+Convention+Hall','image_url'=>'','phone'=>'+855 35 000 004','website'=>''],
            ['name'=>'Four Rivers Floating Lodge','area'=>'Tatai, Koh Kong','description'=>'Luxury floating lodge on the Tatai River — exclusive venue for intimate events.','capacity_range'=>'10-50','price_per_person'=>'$80-180','best_for'=>'VIP Event, Intimate Wedding','address'=>'Tatai, Koh Kong Province','google_maps'=>'https://maps.google.com/?q=Four+Rivers+Floating+Lodge+Koh+Kong','image_url'=>'','phone'=>'+855 35 000 005','website'=>''],
        ];

        // ── KRATIE ───────────────────────────────────────────────────────────
        $kratie = [
            ['name'=>'Kratie Riverside Hotel','area'=>'Mekong Riverside, Kratie','description'=>'Riverside hotel on the Mekong with garden event spaces and Irrawaddy dolphin views.','capacity_range'=>'50-200','price_per_person'=>'$12-30','best_for'=>'Wedding, Social Event','address'=>'Mekong Riverside, Kratie Town','google_maps'=>'https://maps.google.com/?q=Kratie+Riverside+Hotel','image_url'=>'','phone'=>'+855 72 971 536','website'=>''],
            ['name'=>'Le Tonle Tourism Training Center','area'=>'Kratie Town','description'=>'Training centre with event facilities, managed by a hospitality school.','capacity_range'=>'30-150','price_per_person'=>'$15-35','best_for'=>'Conference, Social Gathering','address'=>'Kratie Town','google_maps'=>'https://maps.google.com/?q=Le+Tonle+Kratie','image_url'=>'','phone'=>'+855 72 000 002','website'=>''],
            ['name'=>'Kratie Provincial Hall','area'=>'Kratie Town','description'=>'Main provincial hall for government and community events.','capacity_range'=>'100-500','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony, Public Event','address'=>'Kratie Town','google_maps'=>'https://maps.google.com/?q=Kratie+Provincial+Hall','image_url'=>'','phone'=>'+855 72 000 003','website'=>''],
            ['name'=>'Dolphin Lodge Kratie','area'=>'Kampi, Kratie','description'=>'Lodge near the Irrawaddy dolphin observation point — unique event setting.','capacity_range'=>'20-100','price_per_person'=>'$20-45','best_for'=>'Eco Event, Intimate Gathering','address'=>'Kampi, Kratie Province','google_maps'=>'https://maps.google.com/?q=Dolphin+Lodge+Kratie','image_url'=>'','phone'=>'+855 72 000 004','website'=>''],
            ['name'=>'Kratie Hotel & Convention','area'=>'Kratie Town Centre','description'=>'Local hotel with convention room for weddings and social events.','capacity_range'=>'50-250','price_per_person'=>'$10-25','best_for'=>'Wedding, Conference','address'=>'Kratie Town','google_maps'=>'https://maps.google.com/?q=Kratie+Hotel+Convention','image_url'=>'','phone'=>'+855 72 000 005','website'=>''],
        ];

        // ── MONDULKIRI ───────────────────────────────────────────────────────
        $mondulkiri = [
            ['name'=>'Nature Lodge Mondulkiri','area'=>'Sen Monorom, Mondulkiri','description'=>'Iconic eco-lodge in the Mondulkiri highlands — perfect for nature and adventure events.','capacity_range'=>'20-100','price_per_person'=>'$25-60','best_for'=>'Retreat, Team Building, Eco Event','address'=>'Sen Monorom, Mondulkiri','google_maps'=>'https://maps.google.com/?q=Nature+Lodge+Mondulkiri','image_url'=>'','phone'=>'+855 12 232 033','website'=>''],
            ['name'=>'Mondulkiri Project (Elephant Valley)','area'=>'Elephant Valley, Mondulkiri','description'=>'Award-winning elephant sanctuary offering unique outdoor event experiences.','capacity_range'=>'20-80','price_per_person'=>'$40-100','best_for'=>'Eco Event, Private Gathering','address'=>'Elephant Valley, Mondulkiri','google_maps'=>'https://maps.google.com/?q=Elephant+Valley+Project+Mondulkiri','image_url'=>'','phone'=>'+855 99 213 026','website'=>''],
            ['name'=>'Mayura Hill Hotel & Resort','area'=>'Sen Monorom, Mondulkiri','description'=>'Hilltop resort with panoramic views of the Mondulkiri highlands and event spaces.','capacity_range'=>'30-150','price_per_person'=>'$20-50','best_for'=>'Wedding, Retreat, Private Event','address'=>'Sen Monorom, Mondulkiri','google_maps'=>'https://maps.google.com/?q=Mayura+Hill+Hotel+Mondulkiri','image_url'=>'','phone'=>'+855 92 000 001','website'=>''],
            ['name'=>'Mondulkiri Provincial Hotel','area'=>'Sen Monorom City','description'=>'Main provincial hotel with function rooms for local events and ceremonies.','capacity_range'=>'50-200','price_per_person'=>'$15-35','best_for'=>'Wedding, Conference, Ceremony','address'=>'Sen Monorom, Mondulkiri','google_maps'=>'https://maps.google.com/?q=Mondulkiri+Provincial+Hotel','image_url'=>'','phone'=>'+855 73 000 001','website'=>''],
            ['name'=>'Bou Sra Waterfall Resort','area'=>'Bou Sra, Mondulkiri','description'=>'Resort near the spectacular Bou Sra Waterfall — unique backdrop for events.','capacity_range'=>'20-100','price_per_person'=>'$20-45','best_for'=>'Outdoor Event, Intimate Gathering','address'=>'Bou Sra Village, Mondulkiri','google_maps'=>'https://maps.google.com/?q=Bou+Sra+Waterfall+Resort+Mondulkiri','image_url'=>'','phone'=>'+855 73 000 002','website'=>''],
        ];

        // ── ODDAR MEANCHEY ───────────────────────────────────────────────────
        $oddarMeanchey = [
            ['name'=>'Samraong Provincial Hotel','area'=>'Samraong City, Oddar Meanchey','description'=>'Main hotel in Samraong City with event facilities for local celebrations.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Wedding, Social Event, Conference','address'=>'Samraong City, Oddar Meanchey','google_maps'=>'https://maps.google.com/?q=Samraong+Hotel+Oddar+Meanchey','image_url'=>'','phone'=>'+855 65 000 001','website'=>''],
            ['name'=>'Oddar Meanchey Convention Hall','area'=>'Samraong City','description'=>'Provincial convention hall for government functions and community events.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony, Public Event','address'=>'Samraong City, Oddar Meanchey','google_maps'=>'https://maps.google.com/?q=Oddar+Meanchey+Convention','image_url'=>'','phone'=>'+855 65 000 002','website'=>''],
            ['name'=>'Anlong Veng Heritage Resort','area'=>'Anlong Veng, Oddar Meanchey','description'=>'Resort near the historic Anlong Veng site — unique venue for heritage events.','capacity_range'=>'30-150','price_per_person'=>'$12-30','best_for'=>'Heritage Event, Social Gathering','address'=>'Anlong Veng, Oddar Meanchey','google_maps'=>'https://maps.google.com/?q=Anlong+Veng+Heritage+Resort','image_url'=>'','phone'=>'+855 65 000 003','website'=>''],
            ['name'=>'Samraong Garden Venue','area'=>'Samraong, Oddar Meanchey','description'=>'Open-air garden space for traditional Cambodian weddings and ceremonies.','capacity_range'=>'100-400','price_per_person'=>'$8-20','best_for'=>'Wedding, Traditional Ceremony','address'=>'Samraong, Oddar Meanchey','google_maps'=>'https://maps.google.com/?q=Samraong+Garden+Venue','image_url'=>'','phone'=>'+855 65 000 004','website'=>''],
            ['name'=>'Oddar Meanchey Guesthouse & Hall','area'=>'Samraong City','description'=>'Guesthouse with adjacent hall suitable for small conferences and social events.','capacity_range'=>'30-150','price_per_person'=>'$8-18','best_for'=>'Conference, Social Event','address'=>'Samraong City, Oddar Meanchey','google_maps'=>'https://maps.google.com/?q=Oddar+Meanchey+Guesthouse','image_url'=>'','phone'=>'+855 65 000 005','website'=>''],
        ];

        // ── PAILIN ───────────────────────────────────────────────────────────
        $pailin = [
            ['name'=>'Pailin Hotel & Event Hall','area'=>'Pailin City','description'=>'Main hotel in Pailin City with event hall for weddings and local celebrations.','capacity_range'=>'50-200','price_per_person'=>'$10-28','best_for'=>'Wedding, Social Event','address'=>'Pailin City','google_maps'=>'https://maps.google.com/?q=Pailin+Hotel','image_url'=>'','phone'=>'+855 53 000 010','website'=>''],
            ['name'=>'Pailin Provincial Convention Hall','area'=>'Pailin City','description'=>'Provincial hall for community events and government ceremonies.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony','address'=>'Pailin City','google_maps'=>'https://maps.google.com/?q=Pailin+Convention+Hall','image_url'=>'','phone'=>'+855 53 000 011','website'=>''],
            ['name'=>'Phnom Yat Resort','area'=>'Phnom Yat, Pailin','description'=>'Resort at the foot of Phnom Yat mountain with scenic views and outdoor spaces.','capacity_range'=>'30-150','price_per_person'=>'$15-35','best_for'=>'Outdoor Event, Retreat','address'=>'Phnom Yat, Pailin Province','google_maps'=>'https://maps.google.com/?q=Phnom+Yat+Resort+Pailin','image_url'=>'','phone'=>'+855 53 000 012','website'=>''],
            ['name'=>'Pailin Gemstone Garden Venue','area'=>'Pailin City Centre','description'=>'Garden venue near the famous gemstone market, ideal for social celebrations.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Wedding, Birthday, Social Event','address'=>'Pailin City','google_maps'=>'https://maps.google.com/?q=Pailin+Garden+Venue','image_url'=>'','phone'=>'+855 53 000 013','website'=>''],
            ['name'=>'Pailin Guesthouse & Banquet','area'=>'Pailin City','description'=>'Local guesthouse with banquet facilities for intimate gatherings.','capacity_range'=>'30-120','price_per_person'=>'$8-20','best_for'=>'Intimate Gathering, Ceremony','address'=>'Pailin City','google_maps'=>'https://maps.google.com/?q=Pailin+Guesthouse+Banquet','image_url'=>'','phone'=>'+855 53 000 014','website'=>''],
        ];

        // ── PREAH VIHEAR ─────────────────────────────────────────────────────
        $preahVihear = [
            ['name'=>'Tbeng Meanchey Hotel','area'=>'Tbeng Meanchey, Preah Vihear','description'=>'Main hotel in the provincial capital with function facilities for events.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Wedding, Conference, Ceremony','address'=>'Tbeng Meanchey, Preah Vihear','google_maps'=>'https://maps.google.com/?q=Tbeng+Meanchey+Hotel','image_url'=>'','phone'=>'+855 64 000 001','website'=>''],
            ['name'=>'Preah Vihear Temple Resort','area'=>'Near Preah Vihear Temple','description'=>'Resort near the UNESCO-listed Preah Vihear Temple with event spaces.','capacity_range'=>'30-150','price_per_person'=>'$15-40','best_for'=>'Heritage Event, Retreat','address'=>'Preah Vihear Province','google_maps'=>'https://maps.google.com/?q=Preah+Vihear+Temple+Resort','image_url'=>'','phone'=>'+855 64 000 002','website'=>''],
            ['name'=>'Preah Vihear Convention Hall','area'=>'Tbeng Meanchey City','description'=>'Provincial convention hall for government and community events.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony','address'=>'Tbeng Meanchey, Preah Vihear','google_maps'=>'https://maps.google.com/?q=Preah+Vihear+Convention','image_url'=>'','phone'=>'+855 64 000 003','website'=>''],
            ['name'=>'Kulen Eco Resort Preah Vihear','area'=>'Preah Vihear Province','description'=>'Eco resort in the remote highland offering unique outdoor event experiences.','capacity_range'=>'20-100','price_per_person'=>'$20-45','best_for'=>'Eco Event, Retreat','address'=>'Preah Vihear Province','google_maps'=>'https://maps.google.com/?q=Kulen+Eco+Resort+Preah+Vihear','image_url'=>'','phone'=>'+855 64 000 004','website'=>''],
            ['name'=>'Tbeng Meanchey Grand Hall','area'=>'Tbeng Meanchey','description'=>'Community grand hall for large local weddings and cultural ceremonies.','capacity_range'=>'100-500','price_per_person'=>'$8-20','best_for'=>'Wedding, Community Event','address'=>'Tbeng Meanchey, Preah Vihear','google_maps'=>'https://maps.google.com/?q=Tbeng+Meanchey+Grand+Hall','image_url'=>'','phone'=>'+855 64 000 005','website'=>''],
        ];

        // ── PREY VENG ────────────────────────────────────────────────────────
        $preyVeng = [
            ['name'=>'Prey Veng Provincial Hotel','area'=>'Prey Veng City','description'=>'Main provincial hotel with banquet facilities for weddings and social events.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Conference, Ceremony','address'=>'Prey Veng City','google_maps'=>'https://maps.google.com/?q=Prey+Veng+Provincial+Hotel','image_url'=>'','phone'=>'+855 43 941 103','website'=>''],
            ['name'=>'Mekong View Resort Prey Veng','area'=>'Mekong Riverside, Prey Veng','description'=>'Riverside resort with Mekong views and garden event spaces.','capacity_range'=>'50-300','price_per_person'=>'$12-30','best_for'=>'Wedding, Social Gathering','address'=>'Mekong Riverside, Prey Veng','google_maps'=>'https://maps.google.com/?q=Mekong+View+Resort+Prey+Veng','image_url'=>'','phone'=>'+855 43 000 002','website'=>''],
            ['name'=>'Prey Veng Convention Hall','area'=>'Prey Veng City','description'=>'Provincial convention centre for government and community functions.','capacity_range'=>'100-500','price_per_person'=>'$8-18','best_for'=>'Conference, Community Event','address'=>'Prey Veng City','google_maps'=>'https://maps.google.com/?q=Prey+Veng+Convention','image_url'=>'','phone'=>'+855 43 000 003','website'=>''],
            ['name'=>'Neak Loeung Riverfront Venue','area'=>'Neak Loeung, Prey Veng','description'=>'Riverfront venue at the Mekong ferry crossing point — scenic outdoor events.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Outdoor Event, Social Gathering','address'=>'Neak Loeung, Prey Veng Province','google_maps'=>'https://maps.google.com/?q=Neak+Loeung+Riverfront','image_url'=>'','phone'=>'+855 43 000 004','website'=>''],
            ['name'=>'Prey Veng Garden Hall','area'=>'Prey Veng City','description'=>'Garden hall for large traditional weddings and Khmer ceremonies.','capacity_range'=>'100-600','price_per_person'=>'$8-22','best_for'=>'Traditional Wedding, Ceremony','address'=>'Prey Veng City','google_maps'=>'https://maps.google.com/?q=Prey+Veng+Garden+Hall','image_url'=>'','phone'=>'+855 43 000 005','website'=>''],
        ];

        // ── PURSAT ───────────────────────────────────────────────────────────
        $pursat = [
            ['name'=>'Pursat Provincial Hotel','area'=>'Pursat City','description'=>'Main hotel in Pursat City with banquet facilities for weddings and events.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Conference, Social Event','address'=>'Pursat City','google_maps'=>'https://maps.google.com/?q=Pursat+Provincial+Hotel','image_url'=>'','phone'=>'+855 52 951 186','website'=>''],
            ['name'=>'Cardamom Mountain Lodge Pursat','area'=>'Cardamom Mountains, Pursat','description'=>'Eco-lodge at the edge of the Cardamom Mountains — ideal for nature retreats and events.','capacity_range'=>'20-100','price_per_person'=>'$20-50','best_for'=>'Retreat, Eco Event','address'=>'Cardamom Mountains, Pursat Province','google_maps'=>'https://maps.google.com/?q=Cardamom+Lodge+Pursat','image_url'=>'','phone'=>'+855 52 000 002','website'=>''],
            ['name'=>'Pursat Riverside Resort','area'=>'Pursat River, Pursat City','description'=>'Riverside resort with garden event spaces along the Pursat River.','capacity_range'=>'50-200','price_per_person'=>'$12-30','best_for'=>'Wedding, Outdoor Event','address'=>'Pursat Riverside, Pursat City','google_maps'=>'https://maps.google.com/?q=Pursat+Riverside+Resort','image_url'=>'','phone'=>'+855 52 000 003','website'=>''],
            ['name'=>'Pursat Convention Hall','area'=>'Pursat City','description'=>'Provincial convention hall for government and large community events.','capacity_range'=>'100-500','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony','address'=>'Pursat City','google_maps'=>'https://maps.google.com/?q=Pursat+Convention+Hall','image_url'=>'','phone'=>'+855 52 000 004','website'=>''],
            ['name'=>'Tonle Sap Floating Village Venue','area'=>'Tonle Sap Lake, Pursat','description'=>'Unique floating village setting on the Tonle Sap Lake for cultural events.','capacity_range'=>'20-100','price_per_person'=>'$15-35','best_for'=>'Cultural Event, Eco Gathering','address'=>'Tonle Sap Lake, Pursat Province','google_maps'=>'https://maps.google.com/?q=Tonle+Sap+Floating+Village+Pursat','image_url'=>'','phone'=>'+855 52 000 005','website'=>''],
        ];

        // ── RATANAKIRI ───────────────────────────────────────────────────────
        $ratanakiri = [
            ['name'=>'Terres Rouges Lodge','area'=>'Banlung, Ratanakiri','description'=>'Classic colonial-style lodge in Banlung — the premier event venue in Ratanakiri.','capacity_range'=>'20-100','price_per_person'=>'$30-70','best_for'=>'Private Event, Retreat, Wedding','address'=>'Banlung, Ratanakiri Province','google_maps'=>'https://maps.google.com/?q=Terres+Rouges+Lodge+Ratanakiri','image_url'=>'','phone'=>'+855 75 974 051','website'=>''],
            ['name'=>'Lake Yeak Lom Eco Resort','area'=>'Yeak Lom Lake, Ratanakiri','description'=>'Eco resort at the sacred volcanic crater lake — breathtaking venue for nature events.','capacity_range'=>'20-80','price_per_person'=>'$25-60','best_for'=>'Eco Event, Intimate Gathering','address'=>'Yeak Lom Lake, Ratanakiri','google_maps'=>'https://maps.google.com/?q=Yeak+Lom+Lake+Resort','image_url'=>'','phone'=>'+855 75 000 002','website'=>''],
            ['name'=>'Ratanakiri Provincial Hotel','area'=>'Banlung City, Ratanakiri','description'=>'Main hotel in Banlung with function facilities for local events.','capacity_range'=>'50-200','price_per_person'=>'$15-38','best_for'=>'Wedding, Conference, Social Event','address'=>'Banlung, Ratanakiri Province','google_maps'=>'https://maps.google.com/?q=Ratanakiri+Provincial+Hotel','image_url'=>'','phone'=>'+855 75 000 003','website'=>''],
            ['name'=>'Banlung Tribal Resort','area'=>'Banlung, Ratanakiri','description'=>'Resort celebrating indigenous Khmer Loeu culture — unique for cultural events.','capacity_range'=>'30-150','price_per_person'=>'$20-50','best_for'=>'Cultural Event, Team Building','address'=>'Banlung, Ratanakiri Province','google_maps'=>'https://maps.google.com/?q=Banlung+Tribal+Resort','image_url'=>'','phone'=>'+855 75 000 004','website'=>''],
            ['name'=>'Ratanakiri Convention Hall','area'=>'Banlung City','description'=>'Provincial convention hall for government and community gatherings.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony, Public Event','address'=>'Banlung, Ratanakiri Province','google_maps'=>'https://maps.google.com/?q=Ratanakiri+Convention+Hall','image_url'=>'','phone'=>'+855 75 000 005','website'=>''],
        ];

        // ── STUNG TRENG ──────────────────────────────────────────────────────
        $stungTreng = [
            ['name'=>'Stung Treng Mekong Hotel','area'=>'Mekong Riverside, Stung Treng','description'=>'Riverside hotel at the Mekong-Sekong confluence with event facilities.','capacity_range'=>'50-200','price_per_person'=>'$12-30','best_for'=>'Wedding, Social Gathering','address'=>'Mekong Riverside, Stung Treng Town','google_maps'=>'https://maps.google.com/?q=Stung+Treng+Mekong+Hotel','image_url'=>'','phone'=>'+855 74 973 739','website'=>''],
            ['name'=>'Mekong Blue Silk Center','area'=>'Stung Treng Town','description'=>'Artisan silk center with event space celebrating Stung Treng silk weaving culture.','capacity_range'=>'20-80','price_per_person'=>'$15-35','best_for'=>'Cultural Event, Private Gathering','address'=>'Stung Treng Town','google_maps'=>'https://maps.google.com/?q=Mekong+Blue+Stung+Treng','image_url'=>'','phone'=>'+855 74 000 002','website'=>''],
            ['name'=>'Stung Treng Provincial Hotel','area'=>'Stung Treng Town','description'=>'Main provincial hotel with basic event facilities.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Wedding, Conference','address'=>'Stung Treng Town','google_maps'=>'https://maps.google.com/?q=Stung+Treng+Provincial+Hotel','image_url'=>'','phone'=>'+855 74 000 003','website'=>''],
            ['name'=>'Preah Rumkel Eco Lodge','area'=>'Preah Rumkel, Stung Treng','description'=>'Eco lodge near the Mekong dolphin habitat — unique venue for eco events.','capacity_range'=>'20-80','price_per_person'=>'$20-45','best_for'=>'Eco Event, Retreat','address'=>'Preah Rumkel Village, Stung Treng','google_maps'=>'https://maps.google.com/?q=Preah+Rumkel+Eco+Lodge','image_url'=>'','phone'=>'+855 74 000 004','website'=>''],
            ['name'=>'Stung Treng Convention Hall','area'=>'Stung Treng Town','description'=>'Provincial convention hall for government and community events.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony','address'=>'Stung Treng Town','google_maps'=>'https://maps.google.com/?q=Stung+Treng+Convention','image_url'=>'','phone'=>'+855 74 000 005','website'=>''],
        ];

        // ── SVAY RIENG ───────────────────────────────────────────────────────
        $svayRieng = [
            ['name'=>'Svay Rieng Provincial Hotel','area'=>'Svay Rieng City','description'=>'Main hotel in Svay Rieng City with banquet facilities for celebrations.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Social Event, Conference','address'=>'Svay Rieng City','google_maps'=>'https://maps.google.com/?q=Svay+Rieng+Provincial+Hotel','image_url'=>'','phone'=>'+855 44 945 028','website'=>''],
            ['name'=>'Bavet International Hotel','area'=>'Bavet City, Svay Rieng','description'=>'International hotel in the Bavet special economic zone with modern event facilities.','capacity_range'=>'50-300','price_per_person'=>'$15-40','best_for'=>'Corporate Event, Wedding','address'=>'Bavet City, Svay Rieng Province','google_maps'=>'https://maps.google.com/?q=Bavet+International+Hotel','image_url'=>'','phone'=>'+855 44 000 002','website'=>''],
            ['name'=>'Svay Rieng Convention Hall','area'=>'Svay Rieng City','description'=>'Provincial convention centre for government and community functions.','capacity_range'=>'100-500','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony, Public Event','address'=>'Svay Rieng City','google_maps'=>'https://maps.google.com/?q=Svay+Rieng+Convention','image_url'=>'','phone'=>'+855 44 000 003','website'=>''],
            ['name'=>'Vietnam Border Garden Venue','area'=>'Near Vietnam Border, Svay Rieng','description'=>'Border-area garden venue popular for large gatherings and trade events.','capacity_range'=>'100-400','price_per_person'=>'$10-25','best_for'=>'Trade Event, Social Gathering','address'=>'Svay Rieng Province near Vietnam Border','google_maps'=>'https://maps.google.com/?q=Svay+Rieng+Border+Garden','image_url'=>'','phone'=>'+855 44 000 004','website'=>''],
            ['name'=>'Svay Rieng River Resort','area'=>'Svay Rieng Province','description'=>'Riverside resort with outdoor event spaces in the Svay Rieng countryside.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Wedding, Outdoor Event','address'=>'Svay Rieng Province','google_maps'=>'https://maps.google.com/?q=Svay+Rieng+River+Resort','image_url'=>'','phone'=>'+855 44 000 005','website'=>''],
        ];

        // ── TAKEO ────────────────────────────────────────────────────────────
        $takeo = [
            ['name'=>'Takeo Provincial Hotel','area'=>'Takeo City','description'=>'Main hotel in Takeo City with banquet hall for weddings and local celebrations.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Social Event, Conference','address'=>'Takeo City','google_maps'=>'https://maps.google.com/?q=Takeo+Provincial+Hotel','image_url'=>'','phone'=>'+855 32 931 555','website'=>''],
            ['name'=>'Tonle Bati Resort','area'=>'Tonle Bati, Takeo','description'=>'Popular resort at the Tonle Bati recreational lake with outdoor event spaces.','capacity_range'=>'100-500','price_per_person'=>'$10-28','best_for'=>'Family Gathering, Outdoor Event','address'=>'Tonle Bati, Takeo Province','google_maps'=>'https://maps.google.com/?q=Tonle+Bati+Resort+Takeo','image_url'=>'','phone'=>'+855 32 000 002','website'=>''],
            ['name'=>'Phnom Chisor Riverside Hotel','area'=>'Takeo Province','description'=>'Hotel near Phnom Chisor hill temple with garden event areas.','capacity_range'=>'50-200','price_per_person'=>'$12-30','best_for'=>'Wedding, Heritage Event','address'=>'Takeo Province','google_maps'=>'https://maps.google.com/?q=Phnom+Chisor+Hotel+Takeo','image_url'=>'','phone'=>'+855 32 000 003','website'=>''],
            ['name'=>'Takeo Convention Hall','area'=>'Takeo City','description'=>'Provincial convention centre for large community and government events.','capacity_range'=>'100-600','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony, Public Event','address'=>'Takeo City','google_maps'=>'https://maps.google.com/?q=Takeo+Convention+Hall','image_url'=>'','phone'=>'+855 32 000 004','website'=>''],
            ['name'=>'Angkor Borei Heritage Resort','area'=>'Angkor Borei, Takeo','description'=>'Resort near the ancient Angkor Borei archaeological site for cultural events.','capacity_range'=>'30-150','price_per_person'=>'$15-35','best_for'=>'Cultural Event, Retreat','address'=>'Angkor Borei, Takeo Province','google_maps'=>'https://maps.google.com/?q=Angkor+Borei+Heritage+Resort','image_url'=>'','phone'=>'+855 32 000 005','website'=>''],
        ];

        // ── TBOUNG KHMUM ─────────────────────────────────────────────────────
        $tboungKhmum = [
            ['name'=>'Tboung Khmum Provincial Hotel','area'=>'Suong City, Tboung Khmum','description'=>'Main hotel in Suong City with event facilities for local celebrations.','capacity_range'=>'50-250','price_per_person'=>'$10-28','best_for'=>'Wedding, Conference, Ceremony','address'=>'Suong City, Tboung Khmum','google_maps'=>'https://maps.google.com/?q=Tboung+Khmum+Provincial+Hotel','image_url'=>'','phone'=>'+855 42 000 010','website'=>''],
            ['name'=>'Suong City Convention Hall','area'=>'Suong City, Tboung Khmum','description'=>'Provincial convention hall for government and community events.','capacity_range'=>'100-400','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony','address'=>'Suong City, Tboung Khmum','google_maps'=>'https://maps.google.com/?q=Suong+Convention+Hall','image_url'=>'','phone'=>'+855 42 000 011','website'=>''],
            ['name'=>'Mekong Garden Resort Tboung Khmum','area'=>'Mekong Riverside, Tboung Khmum','description'=>'Garden resort along the Mekong River with outdoor event spaces.','capacity_range'=>'50-300','price_per_person'=>'$12-30','best_for'=>'Wedding, Outdoor Event','address'=>'Mekong Riverside, Tboung Khmum','google_maps'=>'https://maps.google.com/?q=Mekong+Garden+Resort+Tboung+Khmum','image_url'=>'','phone'=>'+855 42 000 012','website'=>''],
            ['name'=>'Rubber Plantation Resort','area'=>'Tboung Khmum Province','description'=>'Unique event venue surrounded by rubber plantations — rustic and atmospheric.','capacity_range'=>'50-200','price_per_person'=>'$10-25','best_for'=>'Outdoor Event, Cultural Gathering','address'=>'Tboung Khmum Province','google_maps'=>'https://maps.google.com/?q=Rubber+Plantation+Resort+Tboung+Khmum','image_url'=>'','phone'=>'+855 42 000 013','website'=>''],
            ['name'=>'Suong Garden Hall','area'=>'Suong City','description'=>'Garden hall for traditional Khmer weddings and large social events.','capacity_range'=>'100-500','price_per_person'=>'$8-22','best_for'=>'Wedding, Traditional Ceremony','address'=>'Suong City, Tboung Khmum','google_maps'=>'https://maps.google.com/?q=Suong+Garden+Hall','image_url'=>'','phone'=>'+855 42 000 014','website'=>''],
        ];

        // ── BANTEAY MEANCHEY ─────────────────────────────────────────────────
        $banteayMeanchey = [
            ['name'=>'Poipet International Hotel','area'=>'Poipet City, Banteay Meanchey','description'=>'International hotel at the Thai border crossing with modern event facilities.','capacity_range'=>'50-300','price_per_person'=>'$20-50','best_for'=>'Corporate Event, Wedding','address'=>'Poipet City, Banteay Meanchey','google_maps'=>'https://maps.google.com/?q=Poipet+International+Hotel','image_url'=>'','phone'=>'+855 54 000 001','website'=>''],
            ['name'=>'Sisophon Provincial Hotel','area'=>'Sisophon City, Banteay Meanchey','description'=>'Main provincial hotel with banquet facilities for weddings and social events.','capacity_range'=>'50-250','price_per_person'=>'$12-30','best_for'=>'Wedding, Conference, Ceremony','address'=>'Sisophon City, Banteay Meanchey','google_maps'=>'https://maps.google.com/?q=Sisophon+Hotel+Banteay+Meanchey','image_url'=>'','phone'=>'+855 54 000 002','website'=>''],
            ['name'=>'Banteay Meanchey Convention Hall','area'=>'Sisophon City','description'=>'Provincial convention hall for government and community events.','capacity_range'=>'100-500','price_per_person'=>'$8-18','best_for'=>'Conference, Ceremony, Public Event','address'=>'Sisophon City, Banteay Meanchey','google_maps'=>'https://maps.google.com/?q=Banteay+Meanchey+Convention','image_url'=>'','phone'=>'+855 54 000 003','website'=>''],
            ['name'=>'Banteay Chhmar Resort','area'=>'Banteay Chhmar, Banteay Meanchey','description'=>'Resort near the ancient Banteay Chhmar temple — heritage event venue.','capacity_range'=>'30-150','price_per_person'=>'$15-35','best_for'=>'Heritage Event, Retreat','address'=>'Banteay Chhmar, Banteay Meanchey','google_maps'=>'https://maps.google.com/?q=Banteay+Chhmar+Resort','image_url'=>'','phone'=>'+855 54 000 004','website'=>''],
            ['name'=>'Poipet Casino Resort Garden','area'=>'Poipet, Banteay Meanchey','description'=>'Large resort complex near the casino zone with event and banquet halls.','capacity_range'=>'100-600','price_per_person'=>'$15-40','best_for'=>'Corporate Event, Gala, Wedding','address'=>'Poipet City, Banteay Meanchey','google_maps'=>'https://maps.google.com/?q=Poipet+Resort+Garden','image_url'=>'','phone'=>'+855 54 000 005','website'=>''],
        ];

        // ── MATCH PROVINCE ───────────────────────────────────────────────────
        return match (true) {
            str_contains($p, 'phnom penh')                               => $phnomPenh,
            str_contains($p, 'siem reap')                                => $siemReap,
            str_contains($p, 'sihanouk') || str_contains($p, 'preah sihanouk') => $sihanoukville,
            str_contains($p, 'battambang')                               => $battambang,
            str_contains($p, 'kampong cham')                             => $kampongCham,
            str_contains($p, 'kampong chhnang')                          => $kampongChhnang,
            str_contains($p, 'kampong speu')                             => $kampongSpeu,
            str_contains($p, 'kampong thom')                             => $kampongThom,
            str_contains($p, 'kampot')                                   => $kampot,
            str_contains($p, 'kandal')                                   => $kandal,
            str_contains($p, 'kep')                                      => $kep,
            str_contains($p, 'koh kong')                                 => $kohKong,
            str_contains($p, 'kratie') || str_contains($p, 'kratié')    => $kratie,
            str_contains($p, 'mondulkiri')                               => $mondulkiri,
            str_contains($p, 'oddar meanchey')                           => $oddarMeanchey,
            str_contains($p, 'pailin')                                   => $pailin,
            str_contains($p, 'preah vihear')                             => $preahVihear,
            str_contains($p, 'prey veng')                                => $preyVeng,
            str_contains($p, 'pursat')                                   => $pursat,
            str_contains($p, 'ratanakiri')                               => $ratanakiri,
            str_contains($p, 'stung treng')                              => $stungTreng,
            str_contains($p, 'svay rieng')                               => $svayRieng,
            str_contains($p, 'takeo') || str_contains($p, 'takéo')      => $takeo,
            str_contains($p, 'tboung khmum')                             => $tboungKhmum,
            str_contains($p, 'banteay meanchey')                         => $banteayMeanchey,
            default                                                       => $phnomPenh,
        };
    }
}
