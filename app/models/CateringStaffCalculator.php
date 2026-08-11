<?php
/**
 * ELLCY — CateringStaffCalculator
 * Looks up the required staff count from the Excel-sourced
 * catering_staff_matrix table (Banana Leaf / Buffet styles).
 * No guessed formulas — every value traces back to the client's
 * source spreadsheets, inserted verbatim in
 * sql/production_update_v2_migration.sql.
 */
class CateringStaffCalculator {

    private const VALID_STYLES = ['banana_leaf', 'buffet'];
    private const VALID_BANDS  = ['0-10', '10-20', '20-30', '30-40'];
    private const VALID_GUESTS = [50,100,150,200,250,300,350,400,450,500,550,600,650,700,750,800,850,900,950,1000];

    /** Exact values from catering data.xlsx; column order follows VALID_BANDS. */
    private const MATRIX = [
        'banana_leaf' => [
            50=>[4,6,7,8],100=>[6,8,11,13],150=>[8,10,13,15],200=>[11,12,15,16],
            250=>[13,13,15,18],300=>[15,15,18,19],350=>[15,16,18,20],400=>[17,18,20,21],
            450=>[18,18,22,23],500=>[21,22,25,26],550=>[21,23,25,27],600=>[23,25,28,30],
            650=>[23,25,28,31],700=>[25,26,30,33],750=>[25,26,30,34],800=>[27,30,32,35],
            850=>[27,30,32,36],900=>[30,32,35,38],950=>[30,32,35,38],1000=>[32,35,37,40],
        ],
        'buffet' => [
            50=>[5,8,9,10],100=>[7,9,11,12],150=>[9,10,11,13],200=>[12,13,14,15],
            250=>[14,15,15,16],300=>[16,17,17,18],350=>[16,18,18,20],400=>[18,18,19,22],
            450=>[18,20,21,24],500=>[22,23,23,24],550=>[22,23,24,26],600=>[24,24,25,28],
            650=>[24,24,26,28],700=>[27,27,27,30],750=>[28,28,30,32],800=>[30,31,32,34],
            850=>[32,32,33,36],900=>[32,32,34,38],950=>[34,34,34,42],1000=>[35,35,36,42],
        ],
    ];

    /**
     * @return array{ok:bool, workers?:int, message?:string}
     */
    public static function calculate(string $style, int $guestCount, string $dishBand): array {
        if (!in_array($style, self::VALID_STYLES, true)) {
            return ['ok' => false, 'message' => 'Invalid serving style.'];
        }
        if (!in_array($dishBand, self::VALID_BANDS, true)) {
            return ['ok' => false, 'message' => 'Invalid dish count band.'];
        }
        if (!in_array($guestCount, self::VALID_GUESTS, true)) {
            return ['ok' => false, 'message' => 'Invalid guest count.'];
        }

        $bandIndex = array_search($dishBand, self::VALID_BANDS, true);
        return ['ok' => true, 'workers' => self::MATRIX[$style][$guestCount][$bandIndex]];
    }

    public static function validGuestCounts(): array { return self::VALID_GUESTS; }
    public static function validDishBands(): array { return self::VALID_BANDS; }
}
