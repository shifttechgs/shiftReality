<?php

return [

    'name'        => 'Reality Shift',
    'tagline'     => 'Question Everything.',
    'description' => 'Reality Shift is an immersive optical illusion experience at Westgate Shopping Complex, Harare. Mind-bending illusions, fun experiences, unforgettable moments. Entry from $15.',
    'url'         => env('APP_URL', 'https://realityshift.co.zw'),

    'contact' => [
        'email'    => 'info@realityshift.co.zw',
        'phone'    => '0790034005',
        'whatsapp' => '263790034005',   // international format, no + or leading 0 — wa.me needs this
    ],

    // Overall Google rating shown in the home hero and reviews — update when it changes
    'google_rating' => 4.8,

    // Public link to the Google reviews. The "Read all reviews on Google" link only
    // appears once this is set, so the page never points at a made-up address.
    'google_reviews_url' => null,

    'location' => [
        'name'         => 'Reality Shift Zimbabwe',
        'address'      => 'Westgate Shopping Complex',
        'city'         => 'Harare',
        'region'       => 'Harare Province',
        'country'      => 'Zimbabwe',
        'country_code' => 'ZW',
        'coordinates'  => [
            'lat' => -17.8252,
            'lng' => 30.9966,
        ],
    ],

    // Opening hours by ISO weekday (1 = Monday … 7 = Sunday), venue local time.
    // null = closed. Drives the live "Open now" status, the hours list and
    // Google's structured data — see App\Support\OpeningHours.
    'timezone' => 'Africa/Harare',
    'hours' => [
        1 => ['09:00', '19:00'],
        2 => ['09:00', '19:00'],
        3 => ['09:00', '19:00'],
        4 => ['09:00', '19:00'],
        5 => ['09:00', '19:00'],
        6 => ['09:00', '19:00'],
        7 => ['09:00', '19:00'],
    ],

    // Booking requests (/book): visitors pick a day, an entry slot and a group size,
    // then the request is emailed to the team, who call to confirm. Nothing is paid online.
    // /groups enquiries go to the same inbox.
    // ⚠ Slot length, last entry and the group-size cap are first guesses — confirm them.
    'booking' => [
        // Every booking request from /book is emailed here (owner's choice, 2026-11-01).
        // BOOKING_EMAIL in .env can list several, comma-separated (e.g. a tester's inbox too)
        'email'              => array_values(array_filter(array_map('trim', explode(',', env('BOOKING_EMAIL', 'bookings@realityshift.co.zw'))))),
        'days_ahead'         => 60,   // how far ahead the date picker reaches, today included (owner's choice, 2026-10-31)
        'slot_minutes'       => 60,   // an entry slot every hour from opening…
        'last_entry_minutes' => 60,   // …until this long before closing
        'max_people'         => 10,   // more than this is a group: pointed to group enquiries
    ],

    'visit' => [
        'duration'    => '1.5 to 2.5 hours',
        'min_age'     => 5,
        'price_from'  => '15',
        'booking_url' => '/book',
    ],

    // Actual rooms at Reality Shift Harare
    // 'image' is a stand-in (hero shots) until real photos of each room are added
    'shifts' => [
        [
            'title'       => 'Upside Down Room',
            'slug'        => 'upside-down',
            'image'       => 'images/hero/hero-1-1200.webp',
            // Ours: a visitor on the ceiling, original colours (2026-11-02, upside.jpeg; the
            // -1600 file is the original's 1086px). 'photo_real': its card on the home page shows the photo, not the drawing
            'photo'       => 'images/rooms/upside-down-800.webp',
            'photo_real'  => true,
            'photo_alt'   => 'A visitor hanging from the ceiling of the Upside Down Room, the café table and chairs below',
            'tagline'     => 'Walk on the ceiling. Question which way is up.',
            'hint'        => 'Scroll, and the room turns. You don\'t.',
            'description' => 'An entire room flipped 180°. Furniture on the ceiling, lights on the floor, and you standing in the middle of it all.',
        ],
        [
            'title'       => 'Stranger in the Mirror',
            'slug'        => 'mirror',
            'image'       => 'images/hero/hero-2-1200.webp',
            // Real visitor photo from the user (2026-11-01): the left half of a two-photo collage,
            // cropped to 4:5. Only 795px wide, so -1600 is 795×994 and -800 is 800×1000.
            'photo'       => 'images/rooms/mirror-800.webp',
            'photo_real'  => true,
            'photo_alt'   => 'A visitor’s face seen through the Stranger in the Mirror’s slatted mirror, her friend’s reflection mixed into the strips between',
            // Home hero slide (a -2064 file with a -1000 twin). Rooms without one show
            // their drawn illusion. ⚠ Ours, from socials, AI-upscaled — swap for the original.
            'hero_photo'  => 'images/hero/kaleidoscope-2064.webp',
            'tagline'     => 'Two people, one face.',
            'hint'        => 'Move around and watch them disobey',
            'description' => 'A multi-mirror installation that splits, multiplies and distorts your image in ways your brain cannot reconcile.',
        ],
        [
            'title'       => 'Infinity Room',
            'slug'        => 'infinity',
            'image'       => 'images/hero/hero-3-1200.webp',
            // Real photo from the user (2026-11-01), centre-cropped to the card's 4:5 from a
            // 1086×1448 original: -800 is 800×1000, -1600 is the full 1086×1358 crop.
            'photo'       => 'images/rooms/infinity-800.webp',
            'photo_real'  => true,
            'photo_alt'   => 'The Infinity Room: purple wisteria hanging from neon-lit ceilings and purple balloons on the floor, repeated endlessly by the mirrored walls',
            'tagline'     => 'A universe that stretches forever in every direction.',
            'hint'        => 'Move to bend the horizon',
            'description' => 'Floor-to-ceiling LED lights reflected in infinite mirrors. You are both at the centre of everything and impossibly small.',
        ],
        [
            'title'       => 'Beuchet Chair',
            'slug'        => 'beuchet',
            'image'       => 'images/hero/hero-1-1200.webp',
            // Real visitor photo from the user (2026-11-01), cropped to the card's 4:5 from a
            // 1049×1499 original: -800 is 800×1000, -1600 is the full 1049×1311 crop.
            'photo'       => 'images/rooms/beuchet-800.webp',
            'photo_real'  => true,
            'photo_alt'   => 'A visitor in a pink jumpsuit leans over the Beuchet chair, cupping her hand around her friend, who looks tiny sitting cross-legged on the seat',
            'tagline'     => 'One person looks giant. The other looks tiny. Same chair.',
            'hint'        => 'Scroll to line up the chair',
            'description' => 'A forced-perspective illusion that makes two people the wrong size at the same time. Perfect for photos.',
        ],
    ],

    // Homepage "Who It's For" card pile — 'route' is where the card leads.
    // Images are stand-ins (hero shots) until real audience photos are added.
    // The About section's photo: [image (≥ 700px wide), alt, where it opens from as it
    // scrolls in (x y, % of its frame: on the logo)]. The walk-in kaleidoscope, shared with Visitor Moments
    'about_photo' => ['images/moments/walk-in-kaleidoscope-800.webp', 'The walk-in kaleidoscope: a triangular mirrored tunnel with the Reality Shift logo on its back wall, lit by LED strips', '50 40'],

    // Every room by name, as the visit band lists them ("The rooms", beside its
    // headline). Longer than 'shifts', which only holds the rooms with a card.
    'room_names' => [
        'Upside Down Room',
        'Stranger in the Mirror',
        'Vortex Tunnel',
        'Infinity Room',
        'Beuchet Chair',
        'Walk-in Kaleidoscope',
        'Tilted Room',
        'Head on a Platter',
        'Ames Room',
    ],

    // The header's "More" panel card ("Question everything. Visit Reality Shift today."):
    // the walk-in kaleidoscope's entrance, upright (2026-11-02)
    'nav_promo_photo' => 'images/moments/walk-in-kaleidoscope-800.webp',

    // Visitor Moments opens on this one: [image, meta, title]. Ours: visitors at the front
    // desk, cropped near-square (as the carousel shows it); the -1600 file is 1001px
    'moments_lead' => ['images/rooms/extra-reception-800.webp', 'At the front desk', 'Your visit starts here'],

    // Our visitors' photos (from Downloads/Reality_Shift_Web_Ready_Images: enhanced, no
    // generative AI), cropped near-square as Visitor Moments shows them, after the
    // kaleidoscope: [image, meta, title]. One per scene: the folder's near-twins
    // (a second upside-down, Beuchet, symmetry and platter shot), its second
    // kaleidoscope and its three illusion posters are left out.
    'visitor_photos' => [
        ['images/moments/beuchet-chair-illusion-800.webp',   'In the room', 'Beuchet Chair'],
        ['images/moments/infinity-mirror-room-800.webp',     'In the room', 'Infinity Room'],
        ['images/moments/symmetry-mirror-room-800.webp',     'In the room', 'Symmetry Room'],
        ['images/moments/head-on-platter-display-800.webp',  'In the room', 'Head on a Platter'],
        ['images/moments/interactive-puzzle-zone-800.webp',  'Between the rooms', 'The puzzle zone'],
        // Added 2026-11-01 from Downloads/reality_shift/enhanced, same 800×868 crop
        ['images/moments/symmetry-room-star-jump-800.webp',     'In the room', 'Symmetry Room'],
        ['images/moments/clone-table-800.webp',                 'In the room', 'Clone Table'],
        ['images/moments/walk-in-kaleidoscope-800.webp',        'In the room', 'Walk-in Kaleidoscope'],
        ['images/moments/head-on-platter-green-dress-800.webp', 'In the room', 'Head on a Platter'],
        ['images/moments/tilted-room-lean-800.webp',            'In the room', 'Tilted Room'],
        // Added 2026-11-02 from Downloads/reality_shift/enhanced/altered, same 800×868 crop
        // (kick came in at 608px wide, so its -800 is upscaled slightly; cook.jpeg replaced
        // head-on-platter-display's files in place, above)
        ['images/moments/beuchet-chair-kick-800.webp',         'In the room', 'Beuchet Chair'],
        ['images/moments/kaleidoscope-floor-pose-800.webp',    'In the room', 'Walk-in Kaleidoscope'],
        ['images/moments/upside-down-room-handstand-800.webp', 'In the room', 'Upside Down Room'],
    ],

    // Extra visitor photos (ours), after the rooms' in Visitor Moments and the visit band
    'hero_extras' => [
        // Visitors at the vortex wall, cropped near-square (as the carousel shows it)
        // to the right-hand visitor and the big vortex; the -1600 file is 944px
        'images/rooms/extra-vortex-800.webp',
    ],
    // The mid-page visit band's full-bleed picture (a -960 file with a -1536 twin, the
    // original's size): our visitors at the vortex wall, the whole width of it
    'band_bg' => 'images/band/vortex-wall-960.webp',

    'audiences' => [
        [
            'name'  => 'Friends',
            'line'  => 'You\'ll be arguing about what you saw for weeks.',
            'route' => 'book',
            'image' => 'images/hero/hero-1-1200.webp',
        ],
        [
            'name'  => 'Date Nights',
            'line'  => 'A first date that never runs out of things to talk about.',
            'route' => 'book',
            'image' => 'images/hero/hero-2-1200.webp',
        ],
        [
            'name'  => 'Families',
            'line'  => 'Ages 5 and up. The grown-ups get fooled just as badly.',
            'route' => 'book',
            'image' => 'images/hero/hero-3-1200.webp',
        ],
        [
            'name'  => 'Content Creators',
            'line'  => 'Every room is built for the camera. Bring a full battery.',
            'route' => 'book',
            'image' => 'images/hero/hero-1-1200.webp',
        ],
        [
            'name'  => 'Tourists',
            'line'  => 'Zimbabwe\'s first optical illusion experience, and a Harare must.',
            'route' => 'book',
            'image' => 'images/hero/hero-2-1200.webp',
        ],
        [
            'name'  => 'Groups & Birthdays',
            'line'  => 'Schools, teams and parties get group rates and priority entry.',
            'route' => 'groups',
            'image' => 'images/hero/hero-3-1200.webp',
        ],
    ],

    // Homepage pricing section: the ticket. 'price' is a number (shown as $N) or null
    // for "On request". :price, :duration and :min_age are filled from 'visit' above,
    // so the price only ever lives in one place. Groups are pitched in their own section.
    'pricing' => [
        'name'     => 'General entry',
        'price'    => ':price',
        'unit'     => 'per person',
        'includes' => [
            'Every room',
            'Go at your own pace. Most visits take :duration',
            'Photos encouraged',
            'Ages :min_age and up',
        ],
        // The home ticket's back, as a real ticket's printed terms: [label, fact].
        // Plain facts, as the venue would print them. :rooms is every room's name, from 'shifts'
        'terms' => [
            ['Rooms',  ':rooms'],
            ['Time',   'No time limit. Most visits take :duration.'],
            ['Photos', 'Welcome in every room.'],
            ['Ages',   ':min_age and up.'],
        ],
    ],

    // Groups (the pricing and groups pages). The image is ours: three friends
    // leaning at impossible angles in the tilted room (2026-11-02)
    'groups' => [
        'image'     => 'images/moments/tilted-room-lean-800.webp',
        'image_alt' => 'Three friends in cowboy hats leaning at impossible angles in the tilted room',
        'occasions' => [
            [
                'title' => 'School Trips',
                'line'  => 'The science of perception, hands-on. A field trip they\'ll actually talk about.',
            ],
            [
                'title' => 'Team Building',
                'line'  => 'Nothing breaks the ice like watching your boss shrink in the Beuchet Chair.',
            ],
            [
                'title' => 'Birthdays',
                'line'  => 'Party photos nobody else has. Every room is a backdrop.',
            ],
        ],
    ],

    // The Reviews section's middle photo, under the featured quote: [image (-800, with a
    // -1600 twin), alt]. Ours: friends leaning in the tilted room (the lead review came with friends)
    'reviews_photo' => ['images/moments/tilted-room-lean-800.webp', 'Three friends leaning at impossible angles in the tilted room'],

    // ⚠ STAND-INS — replace with real reviews (from Google) before launch.
    // The first one is shown large as the featured quote; each appears once.
    'testimonials' => [
        [
            'quote'  => 'I\'ve never felt so genuinely confused by a room. We stayed two hours longer than planned. Mind completely blown.',
            'name'   => 'Tatenda M.',
            'detail' => 'Visited with friends',
            'rating' => 5,
        ],
        [
            'quote'  => 'Best experience in Zimbabwe, full stop. My kids are still arguing about what they actually saw. Perfect for the whole family.',
            'name'   => 'Sarah K.',
            'detail' => 'Family visit',
            'rating' => 5,
        ],
        [
            'quote'  => 'Our team-building afternoon became a two-hour debate about reality. 10/10. We\'re already planning to come back.',
            'name'   => 'Farai N.',
            'detail' => 'Corporate group',
            'rating' => 5,
        ],
        [
            'quote'  => 'We came on a date and spent the whole time taking photos of each other. The Beuchet Chair had us crying with laughter.',
            'name'   => 'Rumbi C.',
            'detail' => 'Date night',
            'rating' => 5,
        ],
    ],

    'faq' => [
        [
            'question' => 'How much does it cost?',
            'answer'   => 'Entry is $15 per person. Group rates and special packages are available. Contact us for details.',
        ],
        [
            'question' => 'Do I need to book in advance?',
            'answer'   => 'We strongly recommend booking ahead, especially on weekends and public holidays. Walk-ins are welcome subject to availability.',
        ],
        [
            'question' => 'Is Reality Shift suitable for children?',
            'answer'   => 'Absolutely. Reality Shift is designed for ages 5 and up. Comfortable shoes are recommended for some installations.',
        ],
        [
            'question' => 'How long does the experience take?',
            'answer'   => 'Most visitors spend between 1.5 and 2.5 hours. There\'s no rush. Explore at your own pace.',
        ],
        [
            'question' => 'Can I take photos inside?',
            'answer'   => 'Yes, and we encourage it. Reality Shift was designed to be shared. Some installations are practically made for your camera.',
        ],
        [
            'question' => 'Where are you located?',
            'answer'   => 'We are at Westgate Shopping Complex, Harare. Contact us on 0790034005 or info@realityshift.co.zw for directions.',
        ],
    ],

    'social' => [
        'instagram' => 'https://www.instagram.com/realityshiftzw',
        'facebook'  => 'https://www.facebook.com/realityshiftzw',
        'tiktok'    => 'https://www.tiktok.com/@realityshiftzw',
        'x'         => null,
    ],

];
