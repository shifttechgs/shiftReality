<?php

return [

    'name'        => 'Reality Shift',
    'tagline'     => 'Question Everything.',
    'description' => 'Reality Shift is an immersive optical illusion experience at Westgate Shopping Complex, Harare. Mind-bending illusions, fun experiences, unforgettable moments. Entry from $15.',
    'url'         => env('APP_URL', 'https://realityshift.co.zw'),

    'contact' => [
        'email'    => 'info@realityshift.co.zw',
        'phone'    => '0790034005',
        'whatsapp' => '0790034005',
    ],

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

    'hours' => [
        ['days' => 'Tuesday – Friday', 'time' => '10:00 – 19:00'],
        ['days' => 'Saturday – Sunday', 'time' => '09:00 – 20:00'],
        ['days' => 'Monday',            'time' => 'Closed'],
    ],

    'visit' => [
        'duration'    => '1.5 – 2.5 hours',
        'min_age'     => 5,
        'price_from'  => '15',
        'booking_url' => '/book',
    ],

    // Actual rooms at Reality Shift Harare
    'shifts' => [
        [
            'number'      => '01',
            'title'       => 'Upside Down Room',
            'slug'        => 'upside-down',
            'tagline'     => 'Walk on the ceiling. Question which way is up.',
            'description' => 'An entire room flipped 180°. Furniture on the ceiling, lights on the floor — and you standing in the middle of it all.',
        ],
        [
            'number'      => '02',
            'title'       => 'Stranger in the Mirror',
            'slug'        => 'mirror',
            'tagline'     => 'Your reflection isn\'t doing what you\'re doing.',
            'description' => 'A multi-mirror installation that splits, multiplies and distorts your image in ways your brain cannot reconcile.',
        ],
        [
            'number'      => '03',
            'title'       => 'Infinity Room',
            'slug'        => 'infinity',
            'tagline'     => 'A universe that stretches forever in every direction.',
            'description' => 'Floor-to-ceiling LED lights reflected in infinite mirrors. You are both at the centre of everything and impossibly small.',
        ],
        [
            'number'      => '04',
            'title'       => 'Beuchet Chair',
            'slug'        => 'beuchet',
            'tagline'     => 'One person looks giant. The other looks tiny. Same chair.',
            'description' => 'A forced-perspective illusion that makes two people the wrong size — simultaneously. Perfect for photos.',
        ],
    ],

    'audiences' => [
        'Friends',
        'Couples',
        'Families',
        'Tourists',
        'School Groups',
        'Corporate Teams',
        'Birthday Groups',
        'Content Creators',
        'Date Nights',
        'Curious Minds',
    ],

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
