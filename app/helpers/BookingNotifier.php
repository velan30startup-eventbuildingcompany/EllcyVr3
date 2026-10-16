<?php
/** Server-side owner notification for a completed customer booking. */
class BookingNotifier {
    public static function send(array $booking): bool {
        $escape = static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $money = static fn(mixed $value): string => '₹' . number_format((float)$value, 0, '.', ',');
        $items = is_array($booking['items'] ?? null) ? $booking['items'] : [];
        $rows = '';
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $title = $escape($item['title'] ?? 'Event service');
            $qty = max(1, (int)($item['qty'] ?? 1));
            $details = array_filter([
                trim((string)($item['package'] ?? '')),
                trim((string)($item['slot'] ?? '')),
            ]);
            $rows .= '<tr><td style="padding:10px;border-bottom:1px solid #eee"><strong>' . $title . '</strong>'
                . ($details ? '<br><small>' . $escape(implode(' · ', $details)) . '</small>' : '')
                . '</td><td style="padding:10px;border-bottom:1px solid #eee;text-align:center">' . $qty . '</td>'
                . '<td style="padding:10px;border-bottom:1px solid #eee;text-align:right">' . $money(((float)($item['price'] ?? 0)) * $qty) . '</td></tr>';
        }
        if ($rows === '') $rows = '<tr><td colspan="3" style="padding:10px">Service details were not supplied.</td></tr>';

        $reference = trim((string)($booking['order_ref'] ?? '')) ?: 'Pending reference';
        $name = trim((string)($booking['name'] ?? 'Customer'));
        $subject = 'New ELLCY Enquiry – ' . $reference . ' – ' . $name;
        $field = static function (string $label, mixed $value) use ($escape): string {
            $shown = trim((string)$value) !== '' ? $escape($value) : '—';
            return '<tr><td style="padding:7px 10px;color:#6b6470;width:150px">' . $escape($label) . '</td><td style="padding:7px 10px;font-weight:600">' . $shown . '</td></tr>';
        };
        $html = '<!doctype html><html><body style="margin:0;background:#f5efff;font-family:Arial,sans-serif;color:#181120">'
            . '<div style="max-width:680px;margin:24px auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #eadcff">'
            . '<div style="padding:22px 26px;background:linear-gradient(110deg,#350078,#7000e8,#b820ef);color:#fff"><h1 style="margin:0;font-size:24px">New ELLCY Enquiry</h1><p style="margin:7px 0 0">Booking reference: ' . $escape($reference) . '</p></div>'
            . '<div style="padding:22px 26px"><table style="width:100%;border-collapse:collapse">'
            . $field('Customer', $name) . $field('Mobile', $booking['phone'] ?? '') . $field('Email', $booking['email'] ?? '')
            . $field('Event', $booking['event_type'] ?? '') . $field('Event date', $booking['event_date'] ?? '')
            . $field('Preferred time', $booking['event_time'] ?? '') . $field('Venue', $booking['event_venue'] ?? '')
            . $field('Guest count', $booking['guest_count'] ?? '') . $field('Notes', $booking['note'] ?? '') . '</table>'
            . '<h2 style="font-size:18px;margin:22px 0 8px">Selected services</h2><table style="width:100%;border-collapse:collapse">'
            . '<thead><tr style="background:#f4eaff"><th style="padding:9px;text-align:left">Service</th><th>Qty</th><th style="padding:9px;text-align:right">Amount</th></tr></thead><tbody>' . $rows . '</tbody></table>'
            . '<p style="text-align:right;font-size:18px"><strong>Estimated total: ' . $money($booking['total'] ?? 0) . '</strong></p>'
            . '<p style="color:#6b6470;font-size:13px">This notification was generated securely by the ELLCY booking form.</p></div></div></body></html>';

        $replyTo = filter_var((string)($booking['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? (string)$booking['email'] : null;
        return Mailer::send(BOOKING_ENQUIRY_EMAIL, $subject, $html, $replyTo);
    }
}
