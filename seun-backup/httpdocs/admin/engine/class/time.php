<?php
date_default_timezone_set('Africa/Lagos');
/**
 * @author seun makinde williams (seuntech)
 * @copyright 2011
 * @version 1.0
 * @tutorial for help or more script visit www.1plusalltutor.com
 * @link http://www.1plusalltutor.com
 * @license * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 */


class timekeeper
{


    function check($current_time)
    {
        $this->current_time = trim($current_time);
        if (!filter_var($this->current_time, FILTER_VALIDATE_INT) || strlen($this->
            current_time) < 9) {
            $this->current_time = strtotime($this->current_time);
        } else {
            $this->current_time = $this->current_time;
        }

        return (int)$this->current_time;
    }

    public function Dayrange($datestr)
    {
        date_default_timezone_set(date_default_timezone_get());

        $dt = self::check($datestr);

        $res['weekstart'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d',
            strtotime('last monday', $dt));
        $res['weekend'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d',
            strtotime('next sunday', $dt));
        $res['weekid'] = date('N', $dt) == 7 ? date('W', $dt) : date('W',
            strtotime('next sunday', $dt));

        $res['monthstart'] = date('Y-m-d', strtotime('first day of this month', $dt));
        $res['monthend'] = date('Y-m-d', strtotime('last day of this month', $dt));
        $res['monthnamefull'] = date('F', strtotime('last day of this month', $dt));


        $res['year'] = date('Y', strtotime('last day of this month', $dt));
        
        if(date("m",  strtotime("+0 day", strtotime($res['weekstart']))) != date('m')){
                for($i=0; $i < 10; $i++){
                 $day = date("Y-m-d",  strtotime("+$i day", strtotime($res['weekstart'])));
                 if(date("m",  strtotime("+0 day", strtotime($day))) == date('m')){
                   $res['weekstart'] = $day;
                   return $res;
                    }
                }
                
            }
        return $res;
    }


    public function getDates($start_date, $end_date, $days)
    {
        //getDates($start,$end,array(1,2,3,4,5))
        // parse the $start_date and $end_date string values
        $stime = new DateTime($start_date);
        $etime = new DateTime($end_date);

        // make a copy so we can increment by day
        $ctime = clone $stime;
        $results = array();
        while ($ctime <= $etime) {

            $dow = $ctime->format("w");
            // assumes $days is array containing integers for Sun (0) - Sat (6)
            if (in_array($dow, $days)) {
                // make a copy to return in results
                $ntime = $ctime->format('Y-m-d');
                $results[] = $ntime;
            }
            // incrememnt by 1 day
            //$ctime=date_add($ctime, date_interval_create_from_date_string('1 days'));
            $ctime->modify("+1 days");
        }

        return $results;
    }

    function time_stamp($current_time)
    {

        $this->current_time = self::check($current_time);
        $this->difference = time() - $this->current_time;

        $this->seconds = $this->difference;
        $this->minutes = round($this->difference / 60);
        $this->hours = round($this->difference / 3600);
        $this->days = round($this->difference / 86400);
        $this->weeks = round($this->difference / 604800);
        $this->months = round($this->difference / 2419200);
        $this->years = round($this->difference / 29030400);


        // Seconds
        if ($this->seconds <= 60) {
            return $this->seconds . " Second ago";
        }


        //Minutes
        if ($this->minutes <= 60) {
            if ($this->minutes == 1) {
                return "1 Minute ago";
            } else {
                return $this->minutes . " Minutes ago";
            }
        }


        //Hours
        if ($this->hours <= 24) {
            if ($this->hours == 1) {
                return "1 hour ago";
            } else {
                return $this->hours . " hours ago";
            }
        }


        //Yesterday
        if ($this->days <= 3) {
            return "yesterday";
        }

        //Days
        if ($this->days <= 7) {
            if ($this->days == 1) {
                return "24 Hours ago";
            } else {
                return $this->days . " days ago";
            }
        }


        //Weeks
        if ($this->weeks <= 4) {
            if ($this->weeks == 1) {
                return "7 days ago";
            } else {
                return $this->weeks . " weeks ago";
            }
        }


        //Months
        if ($this->months <= 12) {
            if ($this->months == 1) {
                return "4 weeks ago";
            } else {
                return self::convert_time($this->current_time, $output = "F j Y - H:ia");
            }
        }


        //Years
        if ($this->years == 1) {
            return "12 months ago";
        } else {
            return self::convert_time($this->current_time, $output = "F j Y - H:ia");
        }

    }


    #time coversion
    function convert_time($time, $output = "Y-m-d H:i:s")
    {
        $this->time = $time;
        $this->output = $output;
        return date($this->output, $this->time);
    }


    function calbar($cment, $smile, $pdate)
    {
        $cment = $cment + 5;
        $smile = $smile + 5;


        $difference = time() - strtotime($pdate);
        $pdate = round($difference / 86400);


        $pdate2 = 0;
        $cdate2 = 0;
        $firstcal = 10;
        if ($cment > 7 && $smile > 5) {
            $firstcal = 15;
        }
        if ($cment > 10 && $smile > 7) {
            $firstcal = 20;
        }
        if ($cment > 12 && $smile > 9) {
            $firstcal = 25;
        }
        if ($cment > 15 && $smile > 12) {
            $firstcal = 30;
        }
        if ($cment > 17 && $smile > 14) {
            $firstcal = 35;
        }
        if ($cment > 20 && $smile > 17) {
            $firstcal = 40;
        }
        if ($cment > 22 && $smile > 19) {
            $firstcal = 45;
        }
        if ($cment > 25 && $smile > 22) {
            $firstcal = 50;
        }
        if ($cment > 27 && $smile > 24) {
            $firstcal = 55;
        }
        if ($cment > 30 && $smile > 27) {
            $firstcal = 60;
        }
        if ($cment > 32 && $smile > 29) {
            $firstcal = 65;
        }
        if ($cment > 35 && $smile > 32) {
            $firstcal = 70;
        }
        if ($cment > 37 && $smile > 34) {
            $firstcal = 75;
        }
        if ($cment > 40 && $smile > 37) {
            $firstcal = 80;
        }
        if ($cment > 42 && $smile > 39) {
            $firstcal = 85;
        }
        if ($cment > 45 && $smile > 42) {
            $firstcal = 90;
        }


        if ($pdate >= 5) {
            $pdate2 = 5;
        }
        if ($pdate >= 10) {
            $pdate2 = 10;
        }
        if ($pdate >= 20) {
            $pdate2 = 20;
        }
        if ($pdate >= 30) {
            $pdate2 = 30;
        }


        $firstcal = $firstcal - self::switchdate($pdate2);

        if ($firstcal < 10) {
            $firstcal = 10;
        }
        return $firstcal;
    }

    function switchdate($val)
    {

        switch ($val) {
            case "30":
                $minus = 40;
                break;

            case "20":
                $minus = 35;
                break;

            case "10":
                $minus = 30;
                break;

            case "5":
                $minus = 20;
                break;

            default:
                $minus = 0;
        }
        return $minus;
    }


    public function convert_date($date)
    {
        $dil = array(
            "/",
            ":",
            "\\",
            "-");

        //control parameters
        $date = str_ireplace($dil, "", $date); //replace foriegn characters in the string
        $date = trim(chunk_split($date, 2, " ")); //break string in two's
        //check that the date is not exceeding the default date range
        if (strlen($date) > 11) {
            $date = substr($date, 0, 11); //get just the first eleven characters
        }

        //**************************************************************
        $date = array_filter(explode(" ", $date)); //ensure theres no empty array
        // print_r($date);

        $par = count($date); //count the array to know haow many parameters we are dealing with
        $this_day = date("d", strtotime("now")); //get day
        $this_month = date("m", strtotime("now")); //get month
        $this_year = date("Y", strtotime("now")); //get year
        //decide what the possible date could mean
        switch ($par) {
            case 2:
                ($date[1] > $this_month) ? $new_date = $this_year . "-" . $date[0] . "-" . $date[1] :
                    $new_date = $this_year . "-" . $date[1] . "-" . $date[0];
                break;

            case 3:
                ($date[2] > $this_month) ? $new_date = $date[0] . $date[1] . "-" . $this_month .
                    "-" . $date[2] : $new_date = $date[0] . $date[1] . "-" . $date[2];
                break;
            case 4:
                ($date[2] > $this_month) ? $new_date = $date[0] . $date[1] . "-" . $date[3] .
                    "-" . $date[2] : $new_date = $date[0] . $date[1] . "-" . $date[2] . "-" . $date[3];
                break;
            default:
                $new_date = $this_year . "-" . $this_month . "-" . $date[0];
                break;
        }
        //***************************************************************************
        $date = date("Y-m-d", strtotime($new_date)); //get the new date in the right format


        if ($date != "1970-01-01") {
            return $date;
        } else {
            return array("message" => 130);
        } //return cleaned date
    }

}
?>