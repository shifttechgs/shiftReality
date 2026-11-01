<?php

namespace Tests\Feature;

use App\Mail\GroupEnquiry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InnerPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-20 08:00', 'Africa/Harare'));
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_every_inner_page_renders_without_dashes_or_placeholders(): void
    {
        foreach (['/experience', '/visit', '/faq', '/groups', '/pricing', '/book'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            // visitor copy only: strip scripts, styles and comments
            $text = strip_tags(preg_replace('/<(script|style)\b.*?<\/\1>|<!--.*?-->/s', '', $html));

            $this->assertStringNotContainsString('being built', $text, $path);
            $this->assertDoesNotMatchRegularExpression('/[\x{2013}\x{2014}]/u', $text, "Dash on $path");
        }
    }

    public function test_exhibits_lists_every_room(): void
    {
        $page = $this->get('/experience')->assertOk();

        foreach (config('shiftreality.shifts') as $room) {
            $page->assertSee($room['title'])->assertSee('id="'.$room['slug'].'"', false);
        }
    }

    public function test_faq_lists_every_question_with_schema(): void
    {
        $page = $this->get('/faq')->assertOk()->assertSee('"@type": "FAQPage"', false);

        foreach (config('shiftreality.faq') as $item) {
            $page->assertSee($item['question'], false);
        }
    }

    private function enquiry(array $overrides = []): array
    {
        return array_merge([
            'occasion' => 'School Trips', 'size' => 40, 'date' => '2026-11-12',
            'name' => 'Tendai Banda', 'organisation' => 'Avondale Primary', 'phone' => '077 123 4567',
        ], $overrides);
    }

    public function test_group_enquiry_is_emailed_to_the_bookings_inbox(): void
    {
        Mail::fake();

        $this->post('/groups', $this->enquiry(['email' => 'tendai@example.com']))
            ->assertRedirect(route('groups').'#enquire')
            ->assertSessionHas('group_sent');

        Mail::assertSent(GroupEnquiry::class, function (GroupEnquiry $mail) {
            $e = $mail->enquiry;

            return $mail->hasTo('bookings@realityshift.co.zw')
                && $mail->hasReplyTo('tendai@example.com')
                && $e['size'] === 40
                && $e['day'] === 'Thursday 12 November 2026'
                && $e['organisation'] === 'Avondale Primary';
        });
    }

    public function test_group_enquiry_email_renders(): void
    {
        $mail = new GroupEnquiry([
            'occasion' => 'Birthdays', 'size' => 14, 'date' => null, 'day' => null,
            'name' => 'Tendai Banda', 'organisation' => null, 'phone' => '077 123 4567',
            'email' => null, 'note' => 'Ages 8 to 10', 'received' => 'Tue 20 Oct at 08:00',
        ]);

        $mail->assertHasSubject('Group enquiry: Birthdays, 14 people, Tendai Banda');
        $mail->assertSeeInHtml('Reply on WhatsApp');
        $mail->assertSeeInHtml('https://wa.me/263771234567?text=Hi%20Tendai', false);
        $mail->assertSeeInText('No date yet');
        $mail->assertSeeInText('Ages 8 to 10');
    }

    public function test_thanks_shows_after_a_group_enquiry(): void
    {
        Mail::fake();

        $this->followingRedirects()->post('/groups', $this->enquiry())
            ->assertOk()
            ->assertSee('Enquiry sent')
            ->assertSee('Thanks, Tendai.', false)
            ->assertDontSee('Send enquiry');
    }

    public function test_group_enquiry_needs_the_basics(): void
    {
        $this->from('/groups')->post('/groups', ['occasion' => 'Wedding', 'size' => 1, 'date' => '2026-10-01', 'phone' => 'call me'])
            ->assertSessionHasErrors(['occasion', 'size', 'date', 'name', 'phone']);
    }

    public function test_a_failed_group_send_comes_back_with_the_answers(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->from('/groups')->post('/groups', $this->enquiry())
            ->assertRedirect('/groups')
            ->assertSessionHasErrors('send')
            ->assertSessionHasInput('organisation', 'Avondale Primary');
    }
}
