<?php
/**
 * Created by AbdolRahman Damia
 * User: Home
 * Date: 1/15/14
 * Time: 3:35 PM
 *
 * You may notice that a variety of array variables logically local
 * to functions are declared globally here.  In JavaScript, construction
 * of an array variable from source code occurs as the code is
 * interpreted.  Making these variables pseudo-globals permits us
 * to avoid overhead constructing and disposing of them in each
 * call on the private function in which whey are used.
 *
 */

namespace Dahatu\Dates;

class DateConverter
{
    const J0000 = 1721424.5;                // Julian date of Gregorian epoch: 0000-01-01
    const J1970 = 2440587.5;                // Julian date at Unix epoch: 1970-01-01
    const JMJD = 2400000.5;                // Epoch of Modified Julian Date system
    const J1900 = 2415020.5;                // Epoch (day 1) of Excel 1900 date system (PC)
    const J1904 = 2416480.5;                // Epoch (day 0) of Excel 1904 date system (Mac)
    const GREGORIAN_EPOCH = 1721425.5;
    const JULIAN_EPOCH = 1721423.5;
    const HEBREW_EPOCH = 347995.5;

    /*  WEEKDAY_BEFORE  --  Return Julian date of given weekday (0 = Sunday)
                                    in the seven days ending on jd.  */
    const FRENCH_REVOLUTIONARY_EPOCH = 2375839.5;

    /*  SEARCH_WEEKDAY  --  Determine the Julian date for:

                    weekday      Day of week desired, 0 = Sunday
                    jd           Julian date to begin search
                    direction    1 = next weekday, -1 = last weekday
                    offset       Offset from jd to begin search
    */
    const ISLAMIC_EPOCH = 1948439.5;

    //  Utility weekday functions, just wrappers for search_weekday
    const IRANIAN_EPOCH = 1948320.5;
    const KURDISH_EPOCH = 1948320.5;
    const  MAYAN_COUNT_EPOCH = 584282.5;
    const BAHAI_EPOCH = 2394646.5;
    private static $ISLAMIC_WEEKDAYS = Array("al-'ahad", "al-'ithnayn", "ath-thalatha'", "al-'arb`a'", "al-khamis", "al-jum`a", "as-sabt");

    //  LEAP_GREGORIAN  --  Is a given year in the Gregorian Calendars a leap year ?
    private static $IRANIAN_WEEKDAYS = Array("Yekshanbeh", "Doshanbeh", "Seshhanbeh", "Chaharshanbeh", "Panjshanbeh", "Jomeh", "Shanbeh");

    //  GREGORIAN_TO_JD  --  Determine Julian day number from Gregorian Calendars date
    private static $KURDISH_WEEKDAYS = Array("Yekšenbe", "Dušenbe", "Sešenbe", "Çwaršenbe", "Pencšenbe", "Cumhe", "Šenbe");
    private static $MAYAN_HAAB_MONTHS = Array("Pop", "Uo", "Zip", "Zotz", "Tzec", "Xul",
        "Yaxkin", "Mol", "Chen", "Yax", "Zac", "Ceh",
        "Mac", "Kankin", "Muan", "Pax", "Kayab", "Cumku", "Uayeb");

    //  JD_TO_GREGORIAN  --  Calculate Gregorian Calendars date from Julian day
    private static $MAYAN_TZOLKIN_MONTHS = Array("Imix", "Ik", "Akbal", "Kan", "Chicchan",
        "Cimi", "Manik", "Lamat", "Muluc", "Oc",
        "Chuen", "Eb", "Ben", "Ix", "Men",
        "Cib", "Caban", "Etznab", "Cauac", "Ahau");

    //  ISO_TO_JULIAN  --  Return Julian day of given ISO year, week, and day
    private static $BAHAI_WEEKDAYS = Array("Jam?l", "Kam?l", "Fid?l", "Id?l",
        "Istijl?l", "Istiql?l", "Jal?l");
    private static $INDIAN_CIVIL_WEEKDAYS = Array("ravivara", "somavara", "mangalavara", "budhavara", "brahaspativara", "sukravara", "sanivara");

    //  JD_TO_ISO  --  Return array of ISO (year, week, day) for Julian day
    private $julianDay = 0;

    //  ISO_DAY_TO_JULIAN  --  Return Julian day of given ISO year, and day of year
    private $NormLeap = Array("Normal year", "Leap year");

    //  JD_TO_ISO_DAY  --  Return array of ISO (year, day_of_year) for Julian day

    public function __construct()
    {
        $this->setDateToToday();
    }

    /*  PAD  --  Pad a string to a given length with a given fill character.  */
    /*   private function pad($str, $howlong, $padwith) {
           return  str_pad($str,$howlong,$padwith);
       }*/

    //  JULIAN_TO_JD  --  Determine Julian day number from Julian Calendars date

    public function setDateToToday()
    {
        /*  The following idiocy is due to bizarre incompatibilities
            in the behaviour of getYear() between Netscrape and
            Exploder.  The ideal solution is to use getFullYear(),
            which returns the actual year number, but that would
            break this code on versions of JavaScript prior to
            1.2.  So, for the moment we use the following code
            which works for all versions of JavaScript and browsers
            for all year numbers greater than 1000.  When we're willing
            to require JavaScript 1.2, this may be replaced by
            the single line:

                document.gregorian.year.value = today.getFullYear();

            Thanks to Larry Gilbert for pointing out this problem.
        */
        $today = date('Y-m-d');
        $arrToday = explode('-', $today);
        $y = $arrToday[0];
        if ($y < 1000) $y += 1900;
        $this->julianDay = $this->gregorian_to_jd($y, $arrToday[1], $arrToday[2]);
    }

    private function gregorian_to_jd($year, $month, $day)
    {
        return (self::GREGORIAN_EPOCH - 1) +
        (365 * ($year - 1)) +
        floor(($year - 1) / 4) +
        (-floor(($year - 1) / 100)) +
        floor(($year - 1) / 400) +
        floor((((367 * $month) - 362) / 12) + (($month <= 2) ? 0 : ($this->leap_gregorian($year) ? -1 : -2)) + $day);
    }

    private function leap_gregorian($year)
    {
        return (($year % 4) == 0) &&
        (!((($year % 100) == 0) && (($year % 400) != 0)));
    }

    //  JD_TO_JULIAN  --  Calculate Julian Calendars date from Julian day

    public function setGregorianDate($gYear, $gMonth, $gDay)
    {
        $this->julianDay = $this->gregorian_to_jd($gYear, $gMonth, $gDay);
    }

    //  HEBREW_TO_JD  --  Determine Julian day from Hebrew date

    public function getGregorianDate()
    {
        $arrDate = $this->jd_to_gregorian($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    //  Is a given Hebrew year a leap year ?

    private function jd_to_gregorian($jd)
    {
        $wjd = floor($jd - 0.5) + 0.5;
        $depoch = $wjd - self::GREGORIAN_EPOCH;
        $quadricent = floor($depoch / 146097);
        $dqc = PositionalAstronomy::mod($depoch, 146097);
        $cent = floor($dqc / 36524);
        $dcent = PositionalAstronomy::mod($dqc, 36524);
        $quad = floor($dcent / 1461);
        $dquad = PositionalAstronomy::mod($dcent, 1461);
        $yindex = floor($dquad / 365);
        $year = ($quadricent * 400) + ($cent * 100) + ($quad * 4) + $yindex;
        if (!(($cent == 4) || ($yindex == 4))) {
            $year++;
        }
        $yearday = $wjd - $this->gregorian_to_jd($year, 1, 1);
        $leapadj = (($wjd < $this->gregorian_to_jd($year, 3, 1)) ? 0 : ($this->leap_gregorian($year) ? 1 : 2));
        $month = floor(((($yearday + $leapadj) * 12) + 373) / 367);
        $day = ($wjd - $this->gregorian_to_jd($year, $month, 1)) + 1;
        return Array('year' => $year, 'month' => $month, 'day' => $day);
    }

    //  How many months are there in a Hebrew year (12 = normal, 13 = leap)

    public function setJulianDate($jYear, $jMonth, $jDay)
    {
        $this->julianDay = $this->julian_to_jd($jYear, $jMonth, $jDay);
    }

    //  Test for delay of start of new year and to avoid
    //  Sunday, Wednesday, and Friday as start of the new year.

    private function julian_to_jd($year, $month, $day)
    {
        /* Adjust negative common era years to the zero-based notation we use.  */
        if ($year < 1) {
            $year++;
        }
        /* Algorithm as given in Meeus, Astronomical Algorithms, Chapter 7, page 61 */
        if ($month <= 2) {
            $year--;
            $month += 12;
        }
        return ((floor((365.25 * ($year + 4716))) +
                floor((30.6001 * ($month + 1))) + $day) - 1524.5);
    }

    //  Check for delay in start of new year due to length of adjacent years

    public function getJulianDate()
    {
        $arrDate = $this->jd_to_julian($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    //  How many days are in a Hebrew year ?

    private function jd_to_julian($td)
    {
        $td += 0.5;
        $z = floor($td);
        $a = $z;
        $b = $a + 1524;
        $c = floor(($b - 122.1) / 365.25);
        $d = floor(365.25 * $c);
        $e = floor(($b - $d) / 30.6001);
        $month = floor(($e < 14) ? ($e - 1) : ($e - 13));
        $year = floor(($month > 2) ? ($c - 4716) : ($c - 4715));
        $day = $b - $d - floor(30.6001 * $e);
        /*  If year is less than 1, subtract one to convert from
            a zero based date system to the common era system in
            which the year -1 (1 B.C.E) is followed by year 1 (1 C.E.).  */
        if ($year < 1) {
            $year--;
        }
        return Array('year' => $year, 'month' => $month, 'day' => $day);
    }

    //  How many days are in a given month of a given year

    public function setIranianDate($iYear, $iMonth, $iDay)
    {
        $this->julianDay = $this->iranian_to_jd($iYear, $iMonth, $iDay);
    }

    //  Finally, wrap it all up into...

    private function iranian_to_jd($year, $month, $day)
    {
        $epbase = $year - (($year >= 0) ? 474 : 473);
        $epyear = 474 + PositionalAstronomy::mod($epbase, 2820);
        return $day +
        (($month <= 7) ?
            (($month - 1) * 31) :
            ((($month - 1) * 30) + 6)
        ) +
        floor((($epyear * 682) - 110) / 2816) +
        ($epyear - 1) * 365 +
        floor($epbase / 2820) * 1029983 +
        (self::IRANIAN_EPOCH - 1);
    }

    /*  JD_TO_HEBREW  --  Convert Julian date to Hebrew date
                              This works by making multiple calls to
                              the inverse function, and is this very
                              slow.  */

    public function getIranianDate()
    {
        $arrDate = $this->jd_to_iranian($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    /*  EQUINOXE_A_PARIS  --  Determine Julian day and fraction of the
                                  September equinox at the Paris meridian in
                                  a given Gregorian year.  */

    private function jd_to_iranian($jd)
    {
        $jd = floor($jd) + 0.5;

        $depoch = $jd - $this->iranian_to_jd(475, 1, 1);
        $cycle = floor($depoch / 1029983);
        $cyear = PositionalAstronomy::mod($depoch, 1029983);
        if ($cyear == 1029982) {
            $ycycle = 2820;
        } else {
            $aux1 = floor($cyear / 366);
            $aux2 = PositionalAstronomy::mod($cyear, 366);
            $ycycle = floor(((2134 * $aux1) + (2816 * $aux2) + 2815) / 1028522) +
                $aux1 + 1;
        }
        $year = $ycycle + (2820 * $cycle) + 474;
        if ($year <= 0) {
            $year--;
        }
        $yday = ($jd - $this->iranian_to_jd($year, 1, 1)) + 1;
        $month = ($yday <= 186) ? ceil($yday / 31) : ceil(($yday - 6) / 30);
        $day = ($jd - $this->iranian_to_jd($year, $month, 1)) + 1;
        return Array('year' => $year, 'month' => $month, 'day' => $day);
    }

    /*  PARIS_EQUINOXE_JD  --  Calculate Julian day during which the
                                   September equinox, reckoned from the Paris
                                   meridian, occurred for a given Gregorian
                                   year.  */

    public function setKurdishDate($iYear, $iMonth, $iDay)
    {
        $this->julianDay = $this->kurdish_to_jd($iYear, $iMonth, $iDay);
    }

    /*  ANNEE_DE_LA_REVOLUTION  --  Determine the year in the French
                                        revolutionary Calendars in which a
                                        given Julian day falls.  Returns an
                                        array of two elements:

                                            [0]  Ann?e de la R?volution
                                            [1]  Julian day number containing
                                                 equinox for this year.
        */

    private function kurdish_to_jd($year, $month, $day)
    {
        $year = $year - 1321;
        return $this->iranian_to_jd($year, $month, $day);
    }

    public function getKurdishDate()
    {
        $arrDate = $this->jd_to_kurdish($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    /*  JD_TO_FRENCH_REVOLUTIONARY  --  Calculate date in the French Revolutionary
                                            Calendars from Julian day.  The five or six
                                            "sansculottides" are considered a thirteenth
                                            month in the results of this function.  */

    private function jd_to_kurdish($jd)
    {
        $ret = $this->jd_to_iranian($jd);
        $ret['year'] += 1321;
        return $ret;
    }

    /*  FRENCH_REVOLUTIONARY_TO_JD  --  Obtain Julian day from a given French
                                            Revolutionary Calendars date.  */

    public function setHebrewDate($hYear, $hMonth, $hDay)
    {
        $this->julianDay = $this->hebrew_to_jd($hYear, $hMonth, $hDay);
    }

    //  LEAP_ISLAMIC  --  Is a given year a leap year in the Islamic Calendars ?

    private function hebrew_to_jd($year, $month, $day)
    {
        $months = $this->hebrew_year_months($year);
        $jd = self::HEBREW_EPOCH + $this->hebrew_delay_1($year) +
            $this->hebrew_delay_2($year) + $day + 1;
        if ($month < 7) {
            for ($mon = 7; $mon <= $months; $mon++) {
                $jd += $this->hebrew_month_days($year, $mon);
            }
            for ($mon = 1; $mon < $month; $mon++) {
                $jd += $this->hebrew_month_days($year, $mon);
            }
        } else {
            for ($mon = 7; $mon < $month; $mon++) {
                $jd += $this->hebrew_month_days($year, $mon);
            }
        }
        return $jd;
    }

    //  ISLAMIC_TO_JD  --  Determine Julian day from Islamic date

    private function hebrew_year_months($year)
    {
        return $this->hebrew_leap($year) ? 13 : 12;
    }

    private function hebrew_leap($year)
    {
        return PositionalAstronomy::mod((($year * 7) + 1), 19) < 7;
    }

    private function hebrew_delay_1($year)
    {
        $months = floor(((235 * $year) - 234) / 19);
        $parts = 12084 + (13753 * $months);
        $day = ($months * 29) + floor($parts / 25920);
        if (PositionalAstronomy::mod((3 * ($day + 1)), 7) < 3) {
            $day++;
        }
        return $day;
    }

    //  JD_TO_ISLAMIC  --  Calculate Islamic date from Julian day

    private function hebrew_delay_2($year)
    {
        $last = $this->hebrew_delay_1($year - 1);
        $present = $this->hebrew_delay_1($year);
        $next = $this->hebrew_delay_1($year + 1);

        return (($next - $present) == 356) ? 2 :
            ((($present - $last) == 382) ? 1 : 0);
    }

    //  LEAP_IRANIAN  --  Is a given year a leap year in the Iranian Calendars ?

    private function hebrew_month_days($year, $month)
    {
        //  First of all, dispose of fixed-length 29 day months
        if ($month == 2 || $month == 4 || $month == 6 ||
            $month == 10 || $month == 13
        ) {
            return 29;
        }
        //  If it's not a leap year, Adar has 29 days
        if ($month == 12 && !$this->hebrew_leap($year)) {
            return 29;
        }
        //  If it's Heshvan, days depend on length of year
        if ($month == 8 && !(PositionalAstronomy::mod($this->hebrew_year_days($year), 10) == 5)) {
            return 29;
        }
        //  Similarly, Kislev varies with the length of year
        if ($month == 9 && (PositionalAstronomy::mod($this->hebrew_year_days($year), 10) == 3)) {
            return 29;
        }
        //  Nope, it's a 30 day month
        return 30;
    }

    //  IRANIAN_TO_JD  --  Determine Julian day from Iranian date

    private function hebrew_year_days($year)
    {
        return $this->hebrew_to_jd($year + 1, 7, 1) - $this->hebrew_to_jd($year, 7, 1);
    }

    public function getHebrewDate()
    {
        $arrDate = $this->jd_to_hebrew($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    private function jd_to_hebrew($jd)
    {
        $jd = floor($jd) + 0.5;
        $count = floor((($jd - self::HEBREW_EPOCH) * 98496.0) / 35975351.0);
        $year = $count - 1;
        for ($i = $count; $jd >= $this->hebrew_to_jd($i, 7, 1); $i++) {
            $year++;
        }
        $first = ($jd < $this->hebrew_to_jd($year, 1, 1)) ? 7 : 1;
        $month = $first;
        for ($i = $first; $jd > $this->hebrew_to_jd($year, $i, $this->hebrew_month_days($year, $i)); $i++) {
            $month++;
        }
        $day = ($jd - $this->hebrew_to_jd($year, $month, 1)) + 1;
        return Array('year' => $year, 'month' => $month, 'day' => $day);
    }

    //  JD_TO_IRANIAN  --  Calculate Iranian date from Julian day

    public function setBahaiDate($bMajor, $bCycle, $bYear, $bMonth, $bDay)
    {
        $this->julianDay = $this->bahai_to_jd($bMajor, $bCycle, $bYear, $bMonth, $bDay);
    }

    //  KURDISH_TO_JD  --  Determine Julian day from Kurdish date

    private function bahai_to_jd($major, $cycle, $year, $month, $day)
    {
        $gy = (361 * ($major - 1)) + (19 * ($cycle - 1)) + ($year - 1) + $this->jd_to_gregorian(self::BAHAI_EPOCH)[0];
        return $this->gregorian_to_jd($gy, 3, 20) + (19 * ($month - 1)) +
        (($month != 20) ? 0 : ($this->leap_gregorian($gy + 1) ? -14 : -15)) + $day;
    }

    public function getBahaiDate()
    {
        $arrDate = $this->jd_to_bahai($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2].'/'.$arrDate[3].'/'.$arrDate[4];
        return $arrDate;
    }

    private function jd_to_bahai($jd)
    {
        $jd = floor($jd) + 0.5;
        $gy = $this->jd_to_gregorian($jd)[0];
        $bstarty = $this->jd_to_gregorian(self::BAHAI_EPOCH)[0];
        $bys = $gy - ($bstarty + ((($this->gregorian_to_jd($gy, 1, 1) <= $jd) && ($jd <= $this->gregorian_to_jd($gy, 3, 20))) ? 1 : 0));
        $major = floor($bys / 361) + 1;
        $cycle = floor(PositionalAstronomy::mod($bys, 361) / 19) + 1;
        $year = PositionalAstronomy::mod($bys, 19) + 1;
        $days = $jd - $this->bahai_to_jd($major, $cycle, $year, 1, 1);
        $bld = $this->bahai_to_jd($major, $cycle, $year, 20, 1);
        $month = ($jd >= $bld) ? 20 : (floor($days / 19) + 1);
        $day = ($jd + 1) - $this->bahai_to_jd($major, $cycle, $year, $month, 1);
        return Array('major' => $major, 'cycle' => $cycle, 'year' => $year, 'month' => $month, 'day' => $day);
    }

    //  JD_TO_KURDISH  --  Calculate Kurdish date from Julian day

    public function setFrenchRevolutionDate($fAn, $fMois, $fDecade, $fJour)
    {
        $this->julianDay = $this->french_revolutionary_to_jd($fAn, $fMois, $fDecade, $fJour);
    }

    //  MAYAN_COUNT_TO_JD  --  Determine Julian day from Mayan long count

    private function french_revolutionary_to_jd($an, $mois, $decade, $jour)
    {
        $guess = self::FRENCH_REVOLUTIONARY_EPOCH + (TropicalYear * (($an - 1) - 1));
        $adr = Array($an - 1, 0);
        while ($adr[0] < $an) {
            $adr = $this->annee_da_la_revolution($guess);
            $guess = $adr[1] + (TropicalYear + 2);
        }
        $equinoxe = $adr[1];
        $jd = $equinoxe + (30 * ($mois - 1)) + (10 * ($decade - 1)) + ($jour - 1);
        return $jd;
    }

    private function annee_da_la_revolution($jd)
    {
        $guess = $this->jd_to_gregorian($jd)[0] - 2;
        $lasteq = $this->paris_equinoxe_jd($guess);
        while ($lasteq > $jd) {
            $guess--;
            $lasteq = $this->paris_equinoxe_jd($guess);
        }
        $nexteq = $lasteq - 1;
        while (!(($lasteq <= $jd) && ($jd < $nexteq))) {
            $lasteq = $nexteq;
            $guess++;
            $nexteq = $this->paris_equinoxe_jd($guess);
        }
        $adr = round(($lasteq - self::FRENCH_REVOLUTIONARY_EPOCH) / TropicalYear) + 1;
        return Array($adr, $lasteq);
    }

    //  JD_TO_MAYAN_COUNT  --  Calculate Mayan long count from Julian day

    private function paris_equinoxe_jd($year)
    {
        $ep = $this->equinoxe_a_paris($year);
        $epg = floor($ep - 0.5) + 0.5;
        return $epg;
    }

    //  JD_TO_MAYAN_HAAB  --  Determine Mayan Haab "month" and day from Julian day

    private function equinoxe_a_paris($year)
    {
        //  September equinox in dynamical time
        $equJED = PositionalAstronomy::equinox($year, 2);
        //  Correct for delta T to obtain Universal time
        $equJD = $equJED - (PositionalAstronomy::deltat($year) / (24 * 60 * 60));
        //  Apply the equation of time to yield the apparent time at Greenwich
        $equAPP = $equJD + PositionalAstronomy::equationOfTime($equJED);
        /*  Finally, we must correct for the constant difference between
            the Greenwich meridian and that of Paris, 2?20'15" to the
            East.  */
        $dtParis = (2 + (20 / 60.0) + (15 / (60 * 60.0))) / 360;
        $equParis = $equAPP + $dtParis;
        return $equParis;
    }

    public function getFrenchRevolutionaryDate()
    {
        $arrDate = $this->jd_to_french_revolutionary($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2].'/'.$arrDate[3];
        return $arrDate;
    }

    //  JD_TO_MAYAN_TZOLKIN  --  Determine Mayan Tzolkin "month" and day from Julian day

    private function jd_to_french_revolutionary($jd)
    {
        $jd = floor($jd) + 0.5;
        $adr = $this->annee_da_la_revolution($jd);
        $an = $adr[0];
        $equinoxe = $adr[1];
        $mois = floor(($jd - $equinoxe) / 30) + 1;
        $jour = ($jd - $equinoxe) % 30;
        $decade = floor($jour / 10) + 1;
        $jour = ($jour % 10) + 1;
        return Array('an' => $an, 'mois' => $mois, 'decade' => $decade, 'jour' => $jour);
    }

    public function setIndianCivilDate($icYear, $icMonth, $icDay)
    {
        $this->julianDay = $this->indian_civil_to_jd($icYear, $icMonth, $icDay);
    }

    //  BAHAI_TO_JD  --  Determine Julian day from Bahai date

    private function indian_civil_to_jd($year, $month, $day)
    {
        $gyear = $year + 78;
        $leap = $this->leap_gregorian($gyear);     // Is this a leap year ?
        $start = $this->gregorian_to_jd($gyear, 3, $leap ? 21 : 22);
        $Caitra = $leap ? 31 : 30;
        if ($month == 1) {
            $jd = $start + ($day - 1);
        } else {
            $jd = $start + $Caitra;
            $m = $month - 2;
            $m = min($m, 5);
            $jd += $m * 31;
            if ($month >= 8) {
                $m = $month - 7;
                $jd += $m * 30;
            }
            $jd += $day - 1;
        }
        return $jd;
    }

    public function getIndianCivilDate()
    {
        $arrDate = $this->jd_to_indian_civil($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    private function jd_to_indian_civil($jd)
    {
        $Saka = 79 - 1;                    // Offset in years from Saka era to Gregorian epoch
        $start = 80;                       // Day offset between Saka and Gregorian

        $jd = floor($jd) + 0.5;
        $greg = $this->jd_to_gregorian($jd);       // Gregorian date for Julian day
        $leap = $this->leap_gregorian($greg[0]);   // Is this a leap year?
        $year = $greg[0] - $Saka;            // Tentative year in Saka era
        $greg0 = $this->gregorian_to_jd($greg[0], 1, 1); // JD at start of Gregorian year
        $yday = $jd - $greg0;                // Day number (0 based) in Gregorian year
        $Caitra = $leap ? 31 : 30;          // Days in Caitra this year

        if ($yday < $start) {
            //  Day is at the end of the preceding Saka year
            $year--;
            $yday += $Caitra + (31 * 5) + (30 * 3) + 10 + $start;
        }
        $yday -= $start;
        if ($yday < $Caitra) {
            $month = 1;
            $day = $yday + 1;
        } else {
            $mday = $yday - $Caitra;
            if ($mday < (31 * 5)) {
                $month = floor($mday / 31) + 2;
                $day = ($mday % 31) + 1;
            } else {
                $mday -= 31 * 5;
                $month = floor($mday / 30) + 7;
                $day = ($mday % 30) + 1;
            }
        }
        return Array('year' => $year, 'month' => $month, 'day' => $day);
    }

    //  JD_TO_BAHAI  --  Calculate Bahai date from Julian day

    public function setIslamicDate($ghYear, $ghMonth, $ghDay)
    {
        $this->julianDay = $this->islamic_to_jd($ghYear, $ghMonth, $ghDay);
    }

    //  INDIAN_CIVIL_TO_JD  --  Obtain Julian day for Indian Civil date

    private function islamic_to_jd($year, $month, $day)
    {
        return ($day +
            ceil(29.5 * ($month - 1)) +
            ($year - 1) * 354 +
            floor((3 + (11 * $year)) / 30) +
            self::ISLAMIC_EPOCH) - 1;
    }

    public function getIslamicDate()
    {
        $arrDate = $this->jd_to_islamic($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    //  JD_TO_INDIAN_CIVIL  --  Calculate Indian Civil date from Julian day

    private function jd_to_islamic($jd)
    {
        $jd = floor($jd) + 0.5;
        $year = floor(((30 * ($jd - self::ISLAMIC_EPOCH)) + 10646) / 10631);
        $month = min(12, ceil(($jd - (29 + $this->islamic_to_jd($year, 1, 1))) / 29.5) + 1);
        $day = ($jd - $this->islamic_to_jd($year, $month, 1)) + 1;
        return Array('year' => $year, 'month' => $month, 'day' => $day);
    }

    /*  setDateToToday  --  Preset the fields in
            the request form to today's date.  */

    public function setISODate($isoYear, $isoWeek, $isoDay)
    {
        $this->julianDay = $this->iso_to_julian($isoYear, $isoWeek, $isoDay);
    }

    private function iso_to_julian($year, $week, $day)
    {
        return $day + $this->n_weeks(0, $this->gregorian_to_jd($year - 1, 12, 28), $week);
    }

    private function n_weeks($weekday, $jd, $nthweek)
    {
        $j = 7 * $nthweek;
        if ($nthweek > 0) {
            $j += $this->previous_weekday($weekday, $jd);
        } else {
            $j += $this->next_weekday($weekday, $jd);
        }
        return $j;
    }

    private function previous_weekday($weekday, $jd)
    {
        return $this->search_weekday($weekday, $jd, -1, 1);
    }

    private function search_weekday($weekday, $jd, $direction, $offset)
    {
        return $this->weekday_before($weekday, $jd + ($direction * $offset));
    }

    private function weekday_before($weekday, $jd)
    {
        return $jd - PositionalAstronomy::jwDay($jd - $weekday);
    }

    private function next_weekday($weekday, $jd)
    {
        return $this->search_weekday($weekday, $jd, 1, 7);
    }

    public function getISODate()
    {
        $arrDate = $this->jd_to_iso($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2];
        return $arrDate;
    }

    private function jd_to_iso($jd)
    {
        $year = $this->jd_to_gregorian($jd - 3)[0];
        if ($jd >= $this->iso_to_julian($year + 1, 1, 1)) {
            $year++;
        }
        $week = floor(($jd - $this->iso_to_julian($year, 1, 1)) / 7) + 1;
        $day = PositionalAstronomy::jwday($jd);
        if ($day == 0) {
            $day = 7;
        }
        return Array('year' => $year, 'week' => $week, 'day' => $day);
    }

    public function setISODayDate($idYear, $idDay)
    {
        $this->julianDay = $this->iso_day_to_julian($idYear, $idDay);
    }

    private function iso_day_to_julian($year, $day)
    {
        return ($day - 1) + $this->gregorian_to_jd($year, 1, 1);
    }

    public function getISODayDate()
    {
        $arrDate = $this->jd_to_iso_day($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1];
        return $arrDate;
    }

    private function jd_to_iso_day($jd)
    {
        $year = $this->jd_to_gregorian($jd)[0];
        $day = floor($jd - $this->gregorian_to_jd($year, 1, 1)) + 1;
        return Array('year' => $year, 'day' => $day);
    }

    public function setMayaCountDate($mBaktun, $mKatun, $mTun, $mUinal, $mKin)
    {
        $this->julianDay = $this->mayan_count_to_jd($mBaktun, $mKatun, $mTun, $mUinal, $mKin);
    }

    private function mayan_count_to_jd($baktun, $katun, $tun, $uinal, $kin)
    {
        return self::MAYAN_COUNT_EPOCH +
        ($baktun * 144000) +
        ($katun * 7200) +
        ($tun * 360) +
        ($uinal * 20) +
        $kin;
    }

    public function getMayaCountDate()
    {
        $arrDate = $this->jd_to_mayan_count($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1].'/'.$arrDate[2].'/'.$arrDate[3].'/'.$arrDate[4];
        return $arrDate;
    }

    private function jd_to_mayan_count($jd)
    {
        $jd = floor($jd) + 0.5;
        $d = $jd - self::MAYAN_COUNT_EPOCH;
        $baktun = floor($d / 144000);
        $d = PositionalAstronomy::mod($d, 144000);
        $katun = floor($d / 7200);
        $d = PositionalAstronomy::mod($d, 7200);
        $tun = floor($d / 360);
        $d = PositionalAstronomy::mod($d, 360);
        $uinal = floor($d / 20);
        $kin = PositionalAstronomy::mod($d, 20);

        return Array('baktun' => $baktun, 'katun' => $katun, 'tun' => $tun, 'unial' => $uinal, 'kin' => $kin);
    }

    public function getMayaHaabDate()
    {
        $arrDate = $this->jd_to_mayan_haab($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1];
        return $arrDate;
    }

    private function jd_to_mayan_haab($jd)
    {
        $jd = floor($jd) + 0.5;
        $lcount = $jd - self::MAYAN_COUNT_EPOCH;
        $day = PositionalAstronomy::mod($lcount + 8 + ((18 - 1) * 20), 365);
        return Array(floor($day / 20) + 1, PositionalAstronomy::mod($day, 20));
    }

    public function getMayaTzolkinDate()
    {
        $arrDate = $this->jd_to_mayan_tzolkin($this->julianDay);
        //return $arrDate[0].'/'.$arrDate[1];
        return $arrDate;
    }

    private function jd_to_mayan_tzolkin($jd)
    {
        $jd = floor($jd) + 0.5;
        $lcount = $jd - self::MAYAN_COUNT_EPOCH;
        return Array(PositionalAstronomy::amod($lcount + 20, 20), PositionalAstronomy::amod($lcount + 4, 13));
    }

    public function getJulianDay()
    {
        return $this->julianDay;
    }

    public function setJulianDay($rawJulianDate)
    {
        $this->julianDay = $rawJulianDate;
    }

    public function isLeapGregorian()
    {
        $arrDate = $this->jd_to_gregorian($this->julianDay);
        return $this->leap_gregorian($arrDate[0]);
    }

    public function isLeapIranian()
    {
        $arrDate = $this->jd_to_iranian($this->julianDay);
        return $this->leap_iranian($arrDate[0]);
    }

    private function leap_iranian($year)
    {
        return (((((($year - (($year > 0) ? 474 : 473)) % 2820) + 474) + 38) * 682) % 2816) < 682;
    }

    public function isLeapIslamic()
    {
        $arrDate = $this->jd_to_islamic($this->julianDay);
        return $this->leap_islamic($arrDate[0]);
    }

    private function leap_islamic($year)
    {
        return ((($year * 11) + 14) % 30) < 11;
    }

    public function isLeapJulian()
    {
        $arrDate = $this->jd_to_julian($this->julianDay);
        return $this->leap_julian($arrDate[0]);
    }

    private function leap_julian($year)
    {
        return PositionalAstronomy::mod($year, 4) == (($year > 0) ? 0 : 3);
    }

    public function isLeapHebrew()
    {
        $arrDate = $this->jd_to_hebrew($this->julianDay);
        return $this->hebrew_leap($arrDate[0]);
    }

    public function addDay($numDay = 1)
    {
        $this->julianDay += $numDay;
    }

    private function nearest_weekday($weekday, $jd)
    {
        return $this->search_weekday($weekday, $jd, 1, 3);
    }

    private function next_or_current_weekday($weekday, $jd)
    {
        return $this->search_weekday($weekday, $jd, 1, 6);
    }

    private function previous_or_current_weekday($weekday, $jd)
    {
        return $this->search_weekday($weekday, $jd, 1, 0);
    }
}

/* TESTED AND WORKED
$t = new calendarConverter();
*/