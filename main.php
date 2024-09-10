<?php
include('db_init.php');
include('get_zone.php');
include('get_data_zone.php');
function monthToNumber($month, $from = 'name', $to = 'number')
{
    $translate = array(
        'name' => array(
            'jan', 'feb', 'mar', 'apr', 'may', 'jun',
            'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
        ),
        'number' => array(
            '01', '02', '03', '04', '05', '06', '07',
            '08', '09', '10', '11', '12'
        )
    );
    $index = array_search($month, $translate[$from]);

    if ($index === false) {
        return null; // Месяц не  найден
    }

    return $translate[$to][$index];
}

//Функция для нахождения суммы дочерних составляющих
function sum_sost($id)
{
    global $conn_piak_pg;
    $month_sums = array(
        "jan" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "feb" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "mar" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "apr" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "may" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "jun" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "jul" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "aug" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "sep" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "oct" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "nov" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        ),
        "dec" => array(
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            'total' => 0
        )
    );
    $finalSums = [];
    $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$id'";
    $stmt = $conn_piak_pg->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $item) {
        $month = $item['month'];
        $decade = $item['decade'];
        $pyat = '1' . $item['pyat'];
        $value = floatval($item['value']);
        $month_sums[$month][$decade] += $value;
        $month_sums[$month][$pyat] += $value;
        $month_sums[$month]['total'] += $value;
    }
    foreach ($month_sums as $month => $values) {

        $sums = [

            'total' => $month_sums[$month]['total'] /= $monthsDays[$month],
            '1' => $month_sums[$month]['1'] /= 10,
            '2' => $month_sums[$month]['2'] /= 10,
            '3' => $month_sums[$month]['3'] /= ($monthsDays[$month] - 20),
            '11' => $month_sums[$month]['11'] /= 5,
            '12' => $month_sums[$month]['12'] /= 5,
            '13' => $month_sums[$month]['13'] /= 5,
            '14' => $month_sums[$month]['14'] /= 5,
            '15' => $month_sums[$month]['15'] /= 5,
            '16' => $month_sums[$month]['16'] /= ($monthsDays[$month] - 25)

        ];


        $finalSums[$month] = $sums;
    }


    return $finalSums;
}


function get_children($item)
{

    global $conn_piak_pg;

    $finals_sum = [];
    $id = $item['id'];
    $sql = "SELECT * FROM dbo.structure WHERE parent = '$id'";
    $stmt = $conn_piak_pg->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $item) {
        if ($item['is_composite'] == 'true') {
            return get_children($item);
        } else {
            $item_id = $item['id'];
            $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$item_id'";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                $month = monthToNumber($row['month']);
                $day = strlen($row['day']) < 2 ? '0' . $row['day'] : $row['day'];
                $day_key = $day . "." . $month;
                if (isset($finals_sum[$day_key])) {
                    $finals_sum[$day_key] += floatval($row['value']);
                } else {
                    $finals_sum[$day_key] = floatval($row['value']);
                }
            }

        }
    }
    return $finals_sum;
}

//Водохранилище
class MainReservoir
{

    private $prihod_data;
    private $rashod_data;
    private $popusk_data;
    private $prihod_sum;
    private $rashod_sum;
    private $popusk_sum;
    private $evap;
    private $prec;
    private $sbros;
    private $garants;
    private $to_Magnit;

    private $dops;
    private $disp_data;
    private $marker;
    private $from_up;
    private $arrival; //приходная часть. Формат данных [{'name': 'Естесственный сток', '01.01': 1, '02.01': 2, ...}, {...}]
    private $consumption; //расохдная часть. Формат данных [{'01.01': 1, '02.01': 2, ...}, {...}]
    private $date; // формат. Формат данных '03.04'. 03 - день, а 04 - месяц
    private $period;
    private $releases; // попуски. Формат данных [{'01.01': 1, '02.01': 2, ...}, {...}]
    private $start_level; //start (Начальный уровень)
    private $k; // Коэффициент для перевода из уровней в испарения

    private $itog_array; // итоговый преобразованный массив объёмеов.

    private $summ_array;
    private $volumes;

    private $squares;
    private $leveles;
    private $defs;
    private $rashod_sost;
    private $prihod_sost;
    private $popusk_sost;
    private $rashod_sost_sum;
    private $prihod_sost_sum;
    private $popusk_sost_sum;


    public static $month_day = [
        '01' => ['january', 31],
        '02' => ['february', 29],
        '03' => ['march', 30],
        '04' => ['april', 31],
        '05' => ['may', 30],
        '06' => ['june', 31],
        '07' => ['july', 30],
        '08' => ['august', 31],
        '09' => ['september', 31],
        '10' => ['october', 30],
        '11' => ['november', 31],
        '12' => ['december', 31],
    ];

    public function __construct($marker, $arrival, $consumption, $date, $releases = [], $start_level, $from_up, $fromMagnit)
    {


        if ($marker == '1295') {
            $monthes = ['06', '07', '08', '09', '10'];
        }


        global $conn_piak_pg;

        require_once 'get_zone.php';

        //Получение составляющих
        $year = substr($date, 0, strpos($date, '-'));

        $start_date = new DateTime("$date");
        $finish_date = new DateTime("$year-12-31");
//        if ($period == 'Год') {
//            $final_date = new DateTime("$year-12-31");
//        }
//        else {
//
//            $final_date = new DateTime("$year-$endMonth-01");
//        }
        $finish_date_str = $finish_date->format('Y-m-d');
        $sql = "SELECT * FROM dbo.structure WHERE uid_vdhr = '$marker' and is_up = 'false'";
        $stmt = $conn_piak_pg->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT id FROM dbo.structure WHERE uid_vdhr = '$marker' and name LIKE '%Гарантированный расход%'";
        $stmt = $conn_piak_pg->prepare($sql);
        $stmt->execute();
        $find = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['id'];


        //Инициализация переменных
        $prihod = [];
        $rashod = [];
        $popusk = [];
        $prihod_sost = [];
        $rashod_sost = [];

        //Разделение данных
        foreach ($rows as $item) {
            //print_r($item);

            switch ($item['block']) {
                case 1:
                    if ($item['is_composite'] == 'false') {
                        $prihod[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    } else {
                        $prihod_sost[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    }
                    break;

                case 2:
                    if ($item['is_composite'] == 'false') {
                        $rashod[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    } else {
                        $rashod_sost[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    }
                    break;
                case 5:
                    if ($item['id'] != $find) {
                        if ($item['is_composite'] == 'false') {
                            $popusk[$item['id']] = [
                                'name' => $item['name'],
                                'block' => $item['block'],
                                'id' => $item['id'],
                                'row' => $item['row']
                            ];
                        }
                        break;
                    }

            }
        }


        usort($prihod, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });
        usort($rashod, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });
        usort($popusk, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });

        //Заполнение массивов суточными данными
        foreach ($prihod as $fields) {

            $id = $fields['id'];
            $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$id' ORDER BY month, day";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {

                $month = monthToNumber($row['month']);
                $day = strlen(strval($row['day'])) < 2 ? "0" . $row['day'] : $row['day'];
                $month_data['name'] = $fields['name'];
                $month_data['row'] = $fields['row'];
                $date_key = $day . '.' . $month;
                $month_data[$date_key] = $row['value'];
            }
            $prihod_data[] = $month_data;

        }


        foreach ($rashod as $id => $fields) {
            $id = $fields['id'];
            $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$id' ORDER BY month, day";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                unset($month);
                $month = monthToNumber($row['month']);
                $day = strlen(strval($row['day'])) < 2 ? "0" . $row['day'] : $row['day'];
                $date_key = $day . '.' . $month;
                $month_data['name'] = $fields['name'];
                $month_data['row'] = $fields['row'];
                $month_data[$date_key] = $row['value'];
            }
            $rashod_data[] = $month_data;
        }
        foreach ($popusk as $id => $fields) {
            $id = $fields['id'];
            $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$id' ORDER BY month, day";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                unset($month);
                $month = monthToNumber($row['month']);
                $day = strlen(strval($row['day'])) < 2 ? "0" . $row['day'] : $row['day'];
                $date_key = $day . '.' . $month;
                $month_data['name'] = $fields['name'];
                $month_data['row'] = $fields['row'];
                $month_data[$date_key] = $row['value'];
            }
            $popusk_data[] = $month_data;
        }

        $data_sost_prihod = [];
        foreach ($prihod_sost as $id => $item) {
            $sum_sost = get_children($item);

            $sums_prihod_sost['name'] = $item['name'];
            $sums_prihod_sost['row'] = $item['row'];
            foreach ($sum_sost as $date_key => $val) {
                $sums_prihod_sost[$date_key] = $val;

            }
            $data_sost_prihod[] = $sums_prihod_sost;
        }

        $data_sost_rashod = [];
        foreach ($rashod_sost as $id => $item) {
            $sum_sost = get_children($item);
            $sums_rashod_sost['name'] = $item['name'];
            $sums_rashod_sost['row'] = $item['row'];
            foreach ($sum_sost as $date_key => $val) {
                $sums_rashod_sost[$date_key] = $val;

            }
            $data_sost_rashod[] = $sums_rashod_sost;
        }


        //Суммирование по дням по блокам
        $sums_prihod = [];
        $sums_rashod = [];
        $sums_popusk = [];
        $sums_prihod_sost = [];
        $sums_rashod_sost = [];
        $sums_popusk_sost = [];

        foreach ($prihod_data as $item) {
            foreach ($item as $key => $value) {
                if ($key != 'name' and $key != 'row') {
                    if (isset($sums_prihod[$key])) {
                        $sums_prihod[$key] += $value;
                    } else {
                        $sums_prihod[$key] = $value;
                    }
                }
            }
        }
        foreach ($sums_prihod as $key => $value) {
            $sums_prihod[$key] += $from_up[$key];
        }
        foreach ($rashod_data as $item) {
            foreach ($item as $key => $value) {
                if ($key != 'name' and $key != 'row') {
                    if (isset($sums_rashod[$key])) {
                        $sums_rashod[$key] += $value;
                    } else {
                        $sums_rashod[$key] = $value;
                    }
                }
            }
        }
        foreach ($popusk_data as $item) {
            foreach ($item as $key => $value) {
                if ($key != 'name' and $key != 'row') {
                    if (isset($sums_popusk[$key])) {
                        $sums_popusk[$key] += $value;
                    } else {
                        $sums_popusk[$key] = $value;
                    }
                }
            }
        }
        $sortedData = [];

// 2. Итерируемся по месяцам
        foreach (range(4, 12) as $month) {
            // 3. Итерируемся по элементам массива, выбирая только те, которые относятся к текущему месяцу
            foreach ($sums_prihod as $key => $value) {
                list($day, $currentMonth) = explode('.', $key);
                if ($currentMonth == $month) {
                    $sortedData[$key] = $value;
                }
            }
        }

// 4. Итерируемся по оставшимся месяцам (с 1 по 3)
        foreach (range(1, 3) as $month) {
            // 5. Добавляем элементы к новому массиву
            foreach ($sums_prihod as $key => $value) {
                list($day, $currentMonth) = explode('.', $key);
                if ($currentMonth == $month) {
                    $sortedData[$key] = $value;
                }
            }
        }

// 6. Вывод отсортированного массива


        //Итоговая сумма по дням
        foreach ($sortedData as $key => $value) {
            $month = monthToNumber(explode('.', $key)[1], 'number', 'name');

            $summ_array[$key] = $value - $sums_rashod[$key] - $sums_popusk[$key];
            if ($marker == '1295') {
                $val1 = $sums_popusk[$key];

                $val2 = $sums_rashod[$key];

            }
        }


        $merged_prihod = array_merge($prihod_data, $data_sost_prihod);
        $merged_rashod = array_merge($rashod_data, $data_sost_rashod);
        //Сортировка итогового
        usort($merged_prihod, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });
        usort($merged_rashod, function ($a, $b) {

            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });
//        uksort($summ_array, function ($a, $b) {
//            // Разделяем ключи на день и месяц
//            list($dayA, $monthA) = explode('.', $a);
//            list($dayB, $monthB) = explode('.', $b);
//
//            // Сравниваем по месяцам, затем по дням
//            if ($monthA == $monthB) {
//                return $dayA - $dayB; // Сравнение по дням
//            }
//            return $monthA - $monthB; // Сравнение по месяцам
//        });


        $V0 = self::level_to_SV($start_level, $marker)['V'];
        $S0 = self::level_to_SV($start_level, $marker)['S'];
        $Vdop = 0;
        $Vdops = [];
        $defs = [];
        $sbros = [];
        $evap = [];
        $prec = [];
        $sql = "SELECT npu, umo FROM dbo.jur_reservoir WHERE uid='$marker'";
        $stmt = $conn_piak_pg->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $levelNpu = $rows[0]['npu'];
        $levelUmo = $rows[0]['umo'];
        $disp_data = [];
        $sql = "SELECT volume FROM dbo.batigr_2 WHERE uid_res='$marker' and level = '$levelNpu'";
        $stmt = $conn_piak_pg->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $Vnpu = $rows[0]['volume'];
        $V10Magnit = 171300000;
        $sql = "SELECT volume FROM dbo.batigr_2 WHERE uid_res='$marker' and level = '$levelUmo'";
        $stmt = $conn_piak_pg->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $Vumo = $rows[0]['volume'];


        $copy_date = clone $start_date;
        $level = $start_level;

        $data = get_data($marker);
        foreach ($summ_array as $key => $val) {

            list($day, $month) = explode('.', $key);

            $month_str = self::$month_day[$month][0];
            $day_garant = $day;
            $month_garant = substr($month_str, 0, 3);
            $day = strlen($day) < 2 ? '0' . $day : $day;
            $key_zone = $month . '.' . $day;

            $garant = get_zone($level, $key_zone, $data);

            if ($month == '02' and $day == '29') {
                $garant = $garants['28.02'];
            }
            $sql = "SELECT value FROM dbo.calendar_data WHERE uid_sost='$find' and num_zone = '$garant' and month = '$month_garant' 
and day = '$day_garant'";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $garant = $rows[0]['value'];

            $garants[$key] = $garant;
            $V = $V0 + ($val - $garant) * 86400;
            $val1 = $val - $garant;

            if ($V < $Vumo) {
                $V = $Vumo;
            }
            $ispar0 = self::get_evaporation_with_level($S0, $marker, $month_str)['prec'];
            $osad0 = self::get_evaporation_with_level($S0, $marker, $month_str)['evap'];

            $V = $V - $ispar0 + $osad0;

            $sql = "SELECT  square FROM dbo.batigr_2 WHERE uid_res='$marker' ORDER BY ABS($V - CAST(volume AS FLOAT)) ASC LIMIT 1";

            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $search = $rows;

            $S1 = $search[0]['square'];
            $S = ($S1 + $S0) / 2;
            $ispar = self::get_evaporation_with_level($S, $marker, $month_str)['prec'];
            $osad = self::get_evaporation_with_level($S, $marker, $month_str)['evap'];

            $ispar_val = $ispar / 86400;
            $osad_val = $osad / 86400;

            $evap[$key] = round($osad / 86400, 3);
            $prec[$key] = round($ispar / 86400, 3);

//            if ($marker == '1295' and $month_str = 'june') {
//                $val3 = $val - $garant - $ispar_val + $osad_val;
//                   "\nval3 = $val3. val = $val. garant = $garant. ispar_val = $ispar_val. osad = $osad_val\n";
//            }
            $V = $V0 + ($val - $garant - $ispar_val + $osad_val) * 86400;
//            if ($marker == '1295') {
//                $val_11 = $val - $garant - $ispar_val + $osad_val;
//                echo "\nval = $val. sum_rash = $val_11. garant = $garant. ispar = $ispar_val. osad = $osad_val\n";
//            }
//            if ($marker == '1295') {
//                if ($key == '16.09') {
//                    echo "\nДата: $key. V = $V\n";
//                    echo "\nДоп: $Vdop\n";
//                }
//            }
            if ($marker == '1295') {
                if ($key == '31.05') {

                    if ($V < $Vnpu) {
                        $dop = ($Vnpu - $V) / 86400;
                        $Vdop += $dop;
                        $Vdops[$key] = $V + $Vdop * 86400 . 'Доп';
                    } else {
                        $dop = 0;
                        $Vdops[$key] = $V . 'Осн';
                    }
                } else if ($key == '01.06' or $key == '31.10') {

                    if ($V + $Vdop * 86400 < $Vnpu) {
                        //echo "\nV = $V. Vnpu = $Vnpu. на $key\n";
                        $dop = ($Vnpu - ($V + $Vdop * 86400)) / 86400;
                        $Vdop += $dop;

                        $Vdops[$key] = $V + $Vdop * 86400 . 'Доп';
                    } else {
                        $dop = 0;
                        $Vdops[$key] = $V . 'Осн';
                    }
                } else if (in_array($month, $monthes)) {

                    if ($V + $Vdop * 86400 < $V10Magnit) {
                        //echo "\nV = $V. Vnpu = $Vnpu. на $key\n";
                        $dop = ($Vnpu - ($V + $Vdop * 86400)) / 86400;

                        $Vdop += $dop;
                        $Vdops[$key] = $V + $Vdop * 86400 . 'Доп';
                    } else {
                        $dop = 0;
                        $Vdops[$key] = $V . 'Осн';
                    }
                } else {
                    $dop = 0;
                }
                $dops[$key] = $dop;
            }


            if ($V < $Vumo) {

                $defs[$key] = ($Vumo - $V) / 86400;
                $sbros[$key] = 0;
                $V = $Vumo;
            } elseif ($V > $Vnpu) {
//                if ($marker == '1295' and $month_str == 'june') {
//                    echo "\nДата: $key. V = $V. Vnpu = $Vnpu. val = $val\n";
//                }
                $sbros[$key] = ($V - $Vnpu) / 86400;
                $defs[$key] = 0;


                $V = $Vnpu;
            } else {
                $defs[$key] = 0;
                $sbros[$key] = 0;
            }
            $V0 = $V;


            $sql = "SELECT level, square FROM dbo.batigr_2 WHERE uid_res='$marker' ORDER BY ABS($V - CAST(volume AS FLOAT)) ASC LIMIT 1";

            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $search = $rows;

            $S1 = $search[0]['square'];
            $level_day = $search[0]['level'];
            $squares[$key] = $S1;
            $levels[$key] = floatval($level_day);
            $disp_data[$key] = floatval($level_day);
            $volumes[$key] = $V;
            $S0 = $S1;
            $level = $level_day;
        }

        $garants['29.02'] = $garants['28.02'];

        if (!is_null($fromMagnit)) {

            foreach ($sums_popusk as $key => $value) {
                if (isset($fromMagnit[$key])) {
                    $sums_popusk[$key] += $sbros[$key] + $garants[$key] + $fromMagnit[$key];
                } else {
                    $sums_popusk[$key] += $sbros[$key] + $garants[$key];
                }

            }
        } else {

            foreach ($sums_popusk as $key => $value) {
                $sums_popusk[$key] += $sbros[$key] + $garants[$key];
            }
        }

//        if ($marker == '1295') {
//
//            echo "\n$Vdop\n";
//            die;
//        }
        $this->dops = $dops;
        $this->prihod_data = $merged_prihod;
        $this->rashod_data = $merged_rashod;
        $this->popusk_data = $popusk_data;
        $this->prihod_sum = $sums_prihod;
        $this->rashod_sum = $sums_rashod;
        $this->popusk_sum = $sums_popusk;
        $this->evap = $evap;
        $this->prec = $prec;
        $this->sbros = $sbros;
        $this->garants = $garants;
        $this->disp_data = $disp_data;
        $this->marker = $marker;
        $this->arrival = $prihod_data;
        $this->consumption = $rashod_data;
        $this->releases = $popusk_data;
        $this->start_level = $start_level;
        $this->date = $date;
        $this->to_Magnit = $sums_popusk;
        $this->summ_array = $summ_array;
        $this->volumes = $volumes;
        $this->squares = $squares;
        $this->leveles = $levels;
        $this->defs = $defs;
        $this->from_up = $from_up;
        $this->rashod_sost = $sums_rashod_sost;
        $this->prihod_sost = $sums_prihod_sost;
        $this->popusk_sost = $sums_popusk_sost;
        $this->prihod_sost_sum = $grouped_prihod_sost;
        $this->rashod_sost_sum = $grouped_rashod_sost;
        $this->popusk_sost_sum = $grouped_popusk_sost;
    }

    public function set_all($marker, $arrival, $consumption, $date, $releases = [], $start_level)
    {
        $this->marker = $marker;
        $this->arrival = $arrival;
        $this->consumption = $consumption;
        $this->date = $date;
        $this->releases = $releases;
        $this->start_level = $start_level;
    }

    public function get_dops()
    {

        return $this->dops;
    }

    public function get_disp()
    {
        return $this->disp_data;
    }

    public static function level_to_SV($level, $marker)
    {
        global $conn_piak_pg;
        $SQL = "select square,volume from dbo.batigr_2 where level = '$level' and uid_res = '$marker'";
        $stmt = $conn_piak_pg->prepare($SQL);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $S = $rows[0]['square'];
        $V = $rows[0]['volume'];

        return ['S' => $S, 'V' => $V];
    }


    public function get_arrival_Magnit()
    {
        return $this->start_array_from_complex;
    }

    public function get_prihod()
    {
        return [$this->prihod_data, $this->prihod_sum];
    }

    public function get_rashod()
    {
        return [$this->rashod_data, $this->rashod_sum];
    }

    public function get_popusk()
    {
        return [$this->popusk_data, $this->popusk_sum];
    }

    public function get_cycle()
    {
        return [$this->evap, $this->prec];
    }

    public function get_sbros()
    {
        return $this->sbros;
    }

    public function get_garant()
    {
        return $this->garants;
    }

    public function get_to_Magnit()
    {
        return $this->to_Magnit;
    }

    public function get_rezhim()
    {
        return [$this->volumes, $this->squares, $this->leveles, $this->defs];
    }




//    public function get_days() {
//        $sum_array = $this->summ_array;
//        $prihod_data = $this->summ_array_prihod;
//        $rashod_data = $this->summ_array_rashod;
//        $popusk_data = $this->summ_array_popusk;
//        foreach($sum_array as $key => $value) {
//            $prih = $prihod_data[$key];
//            $rash = $rashod_data[$key];
//            $pop = $popusk_data[$key];
//            echo "\nДата: $key. Приход: $prih. Расход: $rash. Попуск: $pop\n";
//        }
//    }


    public static function get_evaporation_with_level($square, $uid, $month)
    {

        global $conn_piak_pg;

        $SQL = "SELECT \"$month\" FROM dbo.precipitation WHERE uid_vdhr = '$uid' AND procent = '75' and is_prec = '0'"; // Используем плейсхолдер :uid
        $stmt = $conn_piak_pg->prepare($SQL);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $k = $rows[0][$month];

        $evap = (float)$k * (int)$square / (30 * 1000);

        $SQL = "SELECT \"$month\" FROM dbo.precipitation WHERE uid_vdhr = '$uid' AND procent = '75' and is_prec = '1'"; // Используем плейсхолдер :uid
        $stmt = $conn_piak_pg->prepare($SQL);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $k = $rows[0][$month];
        $prec = (float)$k * (int)$square / (30 * 1000);

        $data = [
            'evap' => $evap,
            'prec' => $prec
        ];

        return $data;
    }


    public function __toString()
    {
        return "Hello world";
    }

}

//Вышележащий участок
class UpReservoir
{

    private $prihod_data;
    private $rashod_data;
    private $prihod_sum;
    private $rashod_sum;
    private $prihod_grouped_values;
    private $rashod_grouped_values;

    private $marker;
    private $arrival; //приходная часть. Формат данных [{'name': 'Естесственный сток', '01.01': 1, '02.01': 2, ...}, {...}]
    private $consumption; //расохдная часть. Формат данных [{'01.01': 1, '02.01': 2, ...}, {...}]
    private $date; // формат. Формат данных '03.04'. 03 - день, а 04 - месяц
    private $start_array_from_complex; // Массив, который передается магнитогорскому вдхр для расчета из верхне-уральского
    private $itog_array; // итоговый преобразованный массив объёмеов.

    private $summ_array;

    private $rashod_sost_sum;
    private $prihod_sost_sum;


    public function __construct($marker, $arrival, $consumption, $date, $start_array_from_complex = null)
    {

        global $conn_piak_pg;
        $fromUral = $start_array_from_complex;

        $sql = "SELECT * FROM dbo.structure WHERE uid_vdhr = '$marker' and is_up = 'true'";
        $stmt = $conn_piak_pg->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //Инициализация переменных
        $prihod = [];
        $rashod = [];

        $prihod_sost = [];
        $rashod_sost = [];


        //Разделение данных
        foreach ($rows as $item) {
            //print_r($item);

            switch ($item['block']) {
                case 1:
                    if ($item['is_composite'] == 'false') {
                        $prihod[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    } else {
                        $prihod_sost[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    }
                    break;

                case 2:
                    if ($item['is_composite'] == 'false') {
                        $rashod[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    } else {
                        $rashod_sost[$item['id']] = [
                            'name' => $item['name'],
                            'block' => $item['block'],
                            'id' => $item['id'],
                            'row' => $item['row']
                        ];
                    }
                    break;

            }
        }

        usort($prihod, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });
        usort($rashod, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });

        //Заполнение массивов суточными данными
        foreach ($prihod as $fields) {

            $id = $fields['id'];
            $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$id' ORDER BY month, day";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {

                $month = monthToNumber($row['month']);
                $day = strlen(strval($row['day'])) < 2 ? "0" . $row['day'] : $row['day'];
                $month_data['name'] = $fields['name'];
                $month_data['row'] = $fields['row'];
                $date_key = $day . '.' . $month;
                $month_data[$date_key] = $row['value'];
            }
            $prihod_data[] = $month_data;

        }


        foreach ($rashod as $id => $fields) {
            $id = $fields['id'];
            $sql = "SELECT * FROM dbo.calendar_data WHERE uid_sost = '$id' ORDER BY month, day";
            $stmt = $conn_piak_pg->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                unset($month);
                $month = monthToNumber($row['month']);
                $day = strlen(strval($row['day'])) < 2 ? "0" . $row['day'] : $row['day'];
                $date_key = $day . '.' . $month;
                $month_data['name'] = $fields['name'];
                $month_data['row'] = $fields['row'];
                $month_data[$date_key] = $row['value'];
            }
            $rashod_data[] = $month_data;
        }


        $data_sost_prihod = [];
        foreach ($prihod_sost as $id => $item) {
            $sum_sost = get_children($item);

            $sums_prihod_sost['name'] = $item['name'];
            $sums_prihod_sost['row'] = $item['row'];
            foreach ($sum_sost as $date_key => $val) {
                $sums_prihod_sost[$date_key] = $val;

            }
            $data_sost_prihod[] = $sums_prihod_sost;
        }

        $data_sost_rashod = [];
        foreach ($rashod_sost as $id => $item) {
            $sum_sost = get_children($item);
            $sums_rashod_sost['name'] = $item['name'];
            $sums_rashod_sost['row'] = $item['row'];
            foreach ($sum_sost as $date_key => $val) {
                $sums_rashod_sost[$date_key] = $val;

            }
            $data_sost_rashod[] = $sums_rashod_sost;
        }

        //Суммирование по дням по блокам
        $sums_prihod = [];
        $sums_rashod = [];


        foreach ($prihod_data as $item) {
            foreach ($item as $key => $value) {
                if ($key != 'name' and $key != 'row') {
                    if (isset($sums_prihod[$key])) {
                        $sums_prihod[$key] += $value;
                    } else {
                        $sums_prihod[$key] = $value;
                    }
                }
            }
        }


        if (!is_null($fromUral)) {
            foreach ($prihod_data as $item) {
                foreach ($item as $key => $value) {
                    if ($key != 'name' and $key != 'row') {
                        $edited_array[$key] = $sums_prihod[$key] + $fromUral[$key];
                    }
                }
            }
        }
        if (isset($edited_array)) {
            foreach ($sums_prihod as $key => &$value) {
                $sums_prihod[$key] = $edited_array[$key];
            }
        }

        foreach ($rashod_data as $item) {
            foreach ($item as $key => $value) {
                if ($key != 'name' and $key != 'row') {
                    if (isset($sums_rashod[$key])) {
                        $sums_rashod[$key] += $value;
                    } else {
                        $sums_rashod[$key] = $value;
                    }
                }
            }
        }


        //Итоговая сумма по дням
        foreach ($sums_prihod as $key => $value) {
            $summ_array[$key] = $value - $sums_rashod[$key];
        }

        //Объединение массивов

        $merged_prihod = array_merge($prihod_data, $data_sost_prihod);
        $merged_rashod = array_merge($rashod_data, $data_sost_rashod);


// Вывод результата

        //Сортировка итогового
        uksort($summ_array, function ($a, $b) {
            // Разделяем ключи на день и месяц
            list($dayA, $monthA) = explode('.', $a);
            list($dayB, $monthB) = explode('.', $b);

            // Сравниваем по месяцам, затем по дням
            if ($monthA == $monthB) {
                return $dayA - $dayB; // Сравнение по дням
            }
            return $monthA - $monthB; // Сравнение по месяцам
        });

        usort($merged_prihod, function ($a, $b) {
            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });
        usort($merged_rashod, function ($a, $b) {

            if ($a['row'] == $b['row']) {
                return 0;
            }
            return ($a['row'] < $b['row']) ? -1 : 1;
        });

        $this->prihod_sum = $sums_prihod;
        $this->rashod_sum = $sums_rashod;
        $this->prihod_data = $merged_prihod;
        $this->rashod_data = $merged_rashod;
        $this->marker = $marker;
        $this->arrival = $prihod_data;
        $this->consumption = $rashod_data;
        $this->date = $date;
        $this->start_array_from_complex = $start_array_from_complex;
        $this->summ_array = $summ_array;
    }

    public function get_prihod()
    {
        return [$this->prihod_data, $this->prihod_sum];
    }


    public function get_rashod()
    {
        return [$this->rashod_data, $this->rashod_sum];
    }

    public function get_up()
    {
        return $this->summ_array;

    }
}

$uid = $post['uid'];
$level = $post['level'];
$date_start = $post['date_start'];

$UralUp = new UpReservoir(1290, [], [], '123');

$arrivalUral = $UralUp->get_up();
$prihodUpUral = $UralUp->get_prihod();
$rashodUpUral = $UralUp->get_rashod();

$Ural = new MainReservoir(1290, [], [], '2020-04-01', [], '380.82', $arrivalUral, null);

$toMagnit = $Ural->get_to_Magnit();

$MagnitUp = new UpReservoir(1295, [], [], '123', $toMagnit);
$arrivalMagnit = $MagnitUp->get_up();
$prihodUpMagnit = $MagnitUp->get_prihod();
$rashodUpMagnit = $MagnitUp->get_rashod();

$Magnit = new MainReservoir(1295, [], [], '2020-04-01', [], '349.23', $arrivalMagnit, null);
$dops = $Magnit->get_dops();
$Ural = new MainReservoir(1290, [], [], '2020-04-01', [], '380.82', $arrivalUral, $dops);
$toMagnit = $Ural->get_to_Magnit();
$dataDispUral = $Ural->get_disp();
$prihodUral = $Ural->get_prihod();
$rashodUral = $Ural->get_rashod();
$popuskUral = $Ural->get_popusk();
$cycleUral = $Ural->get_cycle();
$sbrosUral = $Ural->get_sbros();
$garantsUral = $Ural->get_garant();
$volumesUral = $Ural->get_rezhim()[0];
$squaresUral = $Ural->get_rezhim()[1];
$levelesUral = $Ural->get_rezhim()[2];
$defsUral = $Ural->get_rezhim()[3];
$MagnitUp = new UpReservoir(1295, [], [], '123', $toMagnit);
$arrivalMagnit = $MagnitUp->get_up();
$prihodUpMagnit = $MagnitUp->get_prihod();
$rashodUpMagnit = $MagnitUp->get_rashod();

$Magnit = new MainReservoir(1295, [], [], '2020-04-01', [], '349.23', $arrivalMagnit, null);
$prihodMagnit = $Magnit->get_prihod();
$rashodMagnit = $Magnit->get_rashod();
$popuskMagnit = $Magnit->get_popusk();
$cycleMagnit = $Magnit->get_cycle();
$sbrosMagnit = $Magnit->get_sbros();
$garantsMagnit = $Magnit->get_garant();
$volumesMagnit = $Magnit->get_rezhim()[0];
$squaresMagnit = $Magnit->get_rezhim()[1];
$levelesMagnit = $Magnit->get_rezhim()[2];
$defsMagnit = $Magnit->get_rezhim()[3];
$dataDispMagnit = $Magnit->get_disp();
//
//$UralUp = new UpReservoir(1290, [], [], '123');
//$arrivalUral = $UralUp->get_up();
//
//
//$Ural = new MainReservoir(1290, [], [], '2020-01-01', [], '380.82', $arrivalUral, $dops);


$monthes = [
    'apr',
    'may',
    'jun',
    'jul',
    'aug',
    'sep',
    'oct',
    'nov',
    'dec',
    'jan',
    'feb',
    'mar'
];

$data = [
    'up_Ural_prihod_sost' => $prihodUpUral[0],
    'up_Ural_rashod_sost' => $rashodUpUral[0],
    'up_Ural_prihod_sum' => $prihodUpUral[1],
    'up_Ural_rashod_sum' => $rashodUpUral[1],
    'up_Ural_total' => $arrivalUral,
    'Ural_prihod_sost' => $prihodUral[0],
    'Ural_rashod_sost' => $rashodUral[0],
    'Ural_prihod_sum' => $prihodUral[1],
    'Ural_rashod_sum' => $rashodUral[1],
    'Ural_popusk_sost' => $popuskUral[0],
    'Ural_popusk_sum' => $popuskUral[1],
    'Ural_evap' => $cycleUral[0],
    'Ural_prec' => $cycleUral[1],
    'Ural_sbros' => $sbrosUral,
    'Ural_garants' => $garantsUral,
    'Ural_disp_data' => $dataDispUral,
    'to_Magnit' => $toMagnit,
    'Ural_volumes' => $volumesUral,
    'Ural_squares' => $squaresUral,
    'Ural_leveles' => $levelesUral,
    'Ural_defs' => $defsUral,
    'up_Magnit_prihod_sost' => $prihodUpMagnit[0],
    'up_Magnit_rashod_sost' => $rashodUpMagnit[0],
    'up_Magnit_prihod_sum' => $prihodUpMagnit[1],
    'up_Magnit_rashod_sum' => $rashodUpMagnit[1],
    'up_Magnit_total' => $arrivalMagnit,
    'Magnit_prihod_sost' => $prihodMagnit[0],
    'Magnit_rashod_sost' => $rashodMagnit[0],
    'Magnit_prihod_sum' => $prihodMagnit[1],
    'Magnit_rashod_sum' => $rashodMagnit[1],
    'Magnit_popusk_sost' => $popuskMagnit[0],
    'Magnit_popusk_sum' => $popuskMagnit[1],
    'Magnit_evap' => $cycleMagnit[0],
    'Magnit_prec' => $cycleMagnit[1],
    'Magnit_sbros' => $sbrosMagnit,
    'Magnit_garants' => $garantsMagnit,
    'Magnit_volumes' => $volumesMagnit,
    'Magnit_squares' => $squaresMagnit,
    'Magnit_leveles' => $levelesMagnit,
    'Magnit_defs' => $defsMagnit,
    'monthes' => $monthes,
    'garants' => $garantsUral,
    'dops' => $dops,
    'Magnit_disp_data' => $dataDispMagnit
];
echo json_encode($data);

