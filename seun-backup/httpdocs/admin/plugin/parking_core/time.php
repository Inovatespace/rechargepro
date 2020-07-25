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


    function check( $current_time )
    {
        $this->current_time = trim( $current_time );
        if ( !filter_var( $this->current_time, FILTER_VALIDATE_INT ) || strlen( $this->
            current_time ) < 9 )
        {
            $this->current_time = strtotime( $this->current_time );
        } else
        {
            $this->current_time = $this->current_time;
        }

        return ( int )$this->current_time;
    }
    

    function time_stamp( $current_time )
    {
        $this->current_time = self::check( $current_time );
        $this->difference = time() - $this->current_time;

        $this->seconds = $this->difference;
        $this->minutes = round( $this->difference / 60 );
        $this->hours = round( $this->difference / 3600 );
        $this->days = round( $this->difference / 86400 );
        $this->weeks = round( $this->difference / 604800 );
        $this->months = round( $this->difference / 2419200 );
        $this->years = round( $this->difference / 29030400 );


        // Seconds
        if ( $this->seconds <= 60 )
        {
            return $this->seconds." Second ago";
        }


        //Minutes
        if ( $this->minutes <= 60 )
        {
            if ( $this->minutes == 1 )
            {
                return "1 Minute ago";
            } else
            {
                return $this->minutes." Minutes ago";
            }
        }


        //Hours
        if ( $this->hours <= 24 )
        {
            if ( $this->hours == 1 )
            {
                return "1 hour ago";
            } else
            {
                return $this->hours." hours ago";
            }
        }


        //Yesterday
        if ( $this->days <= 3 )
        {
            return "yesterday";
        }

        //Days
        if ( $this->days <= 7 )
        {
            if ( $this->days == 1 )
            {
                return "24 Hours ago";
            } else
            {
                return $this->days." days ago";
            }
        }


        //Weeks
        if ( $this->weeks <= 4 )
        {
            if ( $this->weeks == 1 )
            {
                return "7 days ago";
            } else
            {
                return $this->weeks." weeks ago";
            }
        }


        //Months
        if ( $this->months <= 12 )
        {
            if ( $this->months == 1 )
            {
                return "4 weeks ago";
            } else
            {
                return self::convert_time($this->current_time, $output = "F j Y - H:ia" );
            }
        }


        //Years
        if ( $this->years == 1 )
        {
            return "12 months ago";
        } else
        {
            return self::convert_time($this->current_time, $output = "F j Y - H:ia" );
        }

    }


function time_convert($starttime,$endtime)
    {
        $this->starttime = self::check( $starttime );
        $this->endtime = self::check( $endtime );
        $this->difference = $this->endtime - $this->starttime;

        $this->seconds = $this->difference;
        $this->minutes = round( $this->difference / 60 );
        $this->hours = round( $this->difference / 3600 );
        $this->days = round( $this->difference / 86400 );
        $this->weeks = round( $this->difference / 604800 );
        $this->months = round( $this->difference / 2419200 );
        $this->years = round( $this->difference / 29030400 );


        // Seconds
        if ( $this->seconds <= 60 )
        {
            return $this->seconds." Second ago";
        }


        //Minutes
        if ( $this->minutes <= 60 )
        {
            if ( $this->minutes == 1 )
            {
                return "1 Minute ago";
            } else
            {
                return $this->minutes." Minutes ago";
            }
        }


        //Hours
        if ( $this->hours <= 24 )
        {
            if ( $this->hours == 1 )
            {
                return "1 hour ago";
            } else
            {
                return $this->hours." hours ago";
            }
        }


        //Yesterday
        if ( $this->days <= 3 )
        {
            return "yesterday";
        }

        //Days
        if ( $this->days <= 7 )
        {
            if ( $this->days == 1 )
            {
                return "24 Hours ago";
            } else
            {
                return $this->days." days ago";
            }
        }


        //Weeks
        if ( $this->weeks <= 4 )
        {
            if ( $this->weeks == 1 )
            {
                return "7 days ago";
            } else
            {
                return $this->weeks." weeks ago";
            }
        }


        //Months
        if ( $this->months <= 12 )
        {
            if ( $this->months == 1 )
            {
                return "4 weeks ago";
            } else
            {
                return self::convert_time($this->current_time, $output = "F j Y - H:ia" );
            }
        }


        //Years
        if ( $this->years == 1 )
        {
            return "12 months ago";
        } else
        {
            return self::convert_time($this->current_time, $output = "F j Y - H:ia" );
        }

    }


    #time coversion
    function convert_time( $time, $output = "Y-m-d H:i:s" )
    {
        $this->time = $time;
        $this->output = $output;
        return date( $this->output, $this->time );
    }
    
    
    
    function calbar($cment,$smile,$pdate){
 $cment = $cment+5;
 $smile = $smile+5;
 
 
 $difference = time() - strtotime($pdate);
 $pdate = round($difference / 86400);
 
 
 $pdate2 = 0; 
 $cdate2 = 0; 
 $firstcal = 10;
if ($cment > 7 && $smile > 5) { $firstcal = 15;}
if ($cment > 10 && $smile > 7) { $firstcal = 20;}
if ($cment > 12 && $smile > 9) { $firstcal = 25;}
if ($cment > 15 && $smile > 12) { $firstcal = 30;}
if ($cment > 17 && $smile > 14) { $firstcal = 35;}
if ($cment > 20 && $smile > 17) { $firstcal = 40;}
if ($cment > 22 && $smile > 19) { $firstcal = 45;}
if ($cment > 25 && $smile > 22) { $firstcal = 50;}
if ($cment > 27 && $smile > 24) { $firstcal = 55;}
if ($cment > 30 && $smile > 27) { $firstcal = 60;}
if ($cment > 32 && $smile > 29) { $firstcal = 65;}
if ($cment > 35 && $smile > 32) { $firstcal = 70;}
if ($cment > 37 && $smile > 34) { $firstcal = 75;}
if ($cment > 40 && $smile > 37) { $firstcal = 80;}
if ($cment > 42 && $smile > 39) { $firstcal = 85;}
if ($cment > 45 && $smile > 42) { $firstcal = 90;}


if ($pdate >= 5 ) {$pdate2 = 5;}
if ($pdate >= 10 ) {$pdate2 = 10;}
if ($pdate >= 20 ) {$pdate2 = 20;}
if ($pdate >= 30 ) {$pdate2 = 30;}


$firstcal = $firstcal - self::switchdate($pdate2);

if ($firstcal < 10) { $firstcal = 10;}
return $firstcal;
}

function switchdate($val){
    
    switch ($val){ 
	case "30": $minus = 40;
	break;

	case "20": $minus = 35;
	break;

	case "10": $minus = 30;
	break;
    
    case "5": $minus = 20;
	break;

	default : $minus = 0;
}
return $minus;
}
   
    


}
$timekeeper = new timekeeper();
?>