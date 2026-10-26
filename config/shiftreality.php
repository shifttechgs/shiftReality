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
        1 => null,
        2 => ['10:00', '19:00'],
        3 => ['10:00', '19:00'],
        4 => ['10:00', '19:00'],
        5 => ['10:00', '19:00'],
        6 => ['09:00', '20:00'],
        7 => ['09:00', '20:00'],
    ],

    // Booking requests (/book): visitors pick a day, an entry slot and a group size,
    // then the request opens in WhatsApp for the team to confirm. Nothing is paid online.
    // ⚠ Slot length, last entry and the group-size cap are first guesses — confirm them.
    'booking' => [
        'days_ahead'         => 14,   // how far ahead the date picker reaches, today included
        'slot_minutes'       => 60,   // an entry slot every hour from opening…
        'last_entry_minutes' => 60,   // …until this long before closing
        'max_people'         => 10,   // more than this is a group: pointed to group enquiries
    ],

    'visit' => [
        'duration'    => '1.5 – 2.5 hours',
        'min_age'     => 5,
        'price_from'  => '15',
        'booking_url' => '/book',
    ],

    // Actual rooms at Reality Shift Harare
    // 'image' is a stand-in (hero shots) until real photos of each room are added
    'shifts' => [
        [
            'number'      => '01',
            'title'       => 'Upside Down Room',
            'slug'        => 'upside-down',
            'image'       => 'images/hero/hero-1-1200.webp',
            'tagline'     => 'Walk on the ceiling. Question which way is up.',
            'hint'        => 'Scroll — the room turns, you don\'t',
            'description' => 'An entire room flipped 180°. Furniture on the ceiling, lights on the floor — and you standing in the middle of it all.',
        ],
        [
            'number'      => '02',
            'title'       => 'Stranger in the Mirror',
            'slug'        => 'mirror',
            'image'       => 'images/hero/hero-2-1200.webp',
            'tagline'     => 'Your reflection isn\'t doing what you\'re doing.',
            'hint'        => 'Move around — watch them disobey',
            'description' => 'A multi-mirror installation that splits, multiplies and distorts your image in ways your brain cannot reconcile.',
        ],
        [
            'number'      => '03',
            'title'       => 'Infinity Room',
            'slug'        => 'infinity',
            'image'       => 'images/hero/hero-3-1200.webp',
            // Hero strip photo (an -800 file with a -1600 twin), in place of the drawn illusion.
            // Experiment / stand-in: "Infinity Window" by Karl Martin Saetren on Unsplash (Unsplash License).
            'photo'       => 'images/rooms/infinity-800.webp',
            'tagline'     => 'A universe that stretches forever in every direction.',
            'hint'        => 'Move to bend the horizon',
            'description' => 'Floor-to-ceiling LED lights reflected in infinite mirrors. You are both at the centre of everything and impossibly small.',
        ],
        [
            'number'      => '04',
            'title'       => 'Beuchet Chair',
            'slug'        => 'beuchet',
            'image'       => 'images/hero/hero-1-1200.webp',
            'tagline'     => 'One person looks giant. The other looks tiny. Same chair.',
            'hint'        => 'Scroll to line up the chair',
            'description' => 'A forced-perspective illusion that makes two people the wrong size — simultaneously. Perfect for photos.',
        ],
    ],

    // Homepage "Who It's For" card pile — 'route' is where the card leads.
    // Images are stand-ins (hero shots) until real audience photos are added.
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
            'line'  => 'Ages 5 and up — the grown-ups get fooled just as badly.',
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
            'line'  => 'Zimbabwe\'s first optical illusion experience — a Harare must.',
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
            'All four rooms',
            'Go at your own pace — most visits take :duration',
            'Photos encouraged',
            'Ages :min_age and up',
        ],
    ],

    // Homepage groups section. The image is a stand-in until a real group photo is added.
    'groups' => [
        'image'     => 'images/hero/hero-1-1200.webp',
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
                'line'  => 'Party photos nobody else has — every room is a backdrop.',
            ],
        ],
    ],

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
    ],

    'faq' => [
        [
            'question' => 'How much does it cost?',
            'answer'   => 'Entry is $15 per person. Group rates and special packages are available — contact us for details.',
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
            'answer'   => 'Most visitors spend between 1.5 and 2.5 hours. There\'s no rush — explore at your own pace.',
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
