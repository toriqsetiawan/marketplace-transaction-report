<?php

if (!function_exists('activateWhenRoute')) {
    function activateWhenRoute($route_name, array $params = [], $cssClass = "active")
    {

        if (is_array($route_name)) {
            foreach ($route_name as $name) {
                if (fnmatch($name, Route::current()->getName())) {

                    if (empty($params)) {
                        return $cssClass;
                    } else {
                        foreach ($params as $key => $value) {
                            if (Route::current()->getParameter($key) == $value) {
                                return $cssClass;
                            }
                        }
                    }
                }
            }
            return '';
        }

        if (fnmatch($route_name, Route::current()->getName())) {
            if (empty($params)) {
                return $cssClass;
            } else {
                foreach ($params as $key => $value) {
                    if (Route::current()->getParameter($key) == $value) {
                        return $cssClass;
                    }
                }
            }
        }

        return '';
    }
}


if (!function_exists('dateIndonesia')) {
    function dateIndonesia($stringTime)
    {
        $arrDay = [
            "Minggu",
            "Senin",
            "Selasa",
            "Rabu",
            "Kamis",
            "Jumat",
            "Sabtu"
        ];
        
        $arrMonth = [
            "",
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        $day = $arrDay[date("w", $stringTime)];
        $date = date("j", $stringTime);
        $month = $arrMonth[date("n", $stringTime)];
        $year = date("Y", $stringTime);

        return $day .", ".$date." ".$month." ".$year;

    }
}

// if (!function_exists('getCounter')) {
//     function getCounter($special = false)
//     {
//         $counter = null;

//         if (!$special) {
//             if (date('l') == 'Saturday') {
//                 $counter = 1;
//             } else if (date('l') == 'Sunday') {
//                 $counter = 2;
//             } else if (date('l') == 'Monday') {
//                 $counter = 3;
//             } else if (date('l') == 'Tuesday') {
//                 $counter = 4;
//             } else if (date('l') == 'Wednesday') {
//                 $counter = 5;
//             } else if (date('l') == 'Thursday') {
//                 $counter = 6;
//             } else if (date('l') == 'Friday') {
//                 $counter = 7;
//             }
//         } else {
//             if (date('l') == 'Saturday') {
//                 $counter = 7;
//             } else if (date('l') == 'Sunday') {
//                 $counter = 1;
//             } else if (date('l') == 'Monday') {
//                 $counter = 2;
//             } else if (date('l') == 'Tuesday') {
//                 $counter = 3;
//             } else if (date('l') == 'Wednesday') {
//                 $counter = 4;
//             } else if (date('l') == 'Thursday') {
//                 $counter = 5;
//             } else if (date('l') == 'Friday') {
//                 $counter = 6;
//             }
//         }

//         return $counter;
//     }
// }
