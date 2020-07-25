<?php
  switch ($position)
    {
        case "Admin Officer":
        case "Account Officer":
        case "Cashier":
        case "Driver":
        case "Truck Driver":
        case "Internal Control":
        case "Head, Finance and Admin":
        case "Gardener":
        case "Front Desk Officer":
        case "Customer Care Officer":
        case "Office Assistant":
        case "Office Assistant Manager":
        case "Secretary to the MD":
        case "Procurement Officer":
        case "Security":
        case "Assitant Admin Officer":
            $answer = "Finance and Admin";
            $code = "FA";
            break;

        case "Marketing Officer":
        case "Business Developer/PR":
        case "Business Developer Officer":
            $answer = "Business Development";
            $code = "BD";
            break;
            

        case "GIS Analyst":
            $answer = "Geographical Information System";
            $code = "GIS";
            break;      

        case "Company Lawyer":
            $answer = "Legal";
            $code = "LG";
            break;

        case "General Manager":
            $answer = "General Manager";
            $code = "GM";
            break;

        case "Human Resources Manager":
            $answer = "Human Resources";
            $code = "HR";
            break;

        case "Managing Director":
            $answer = "Managing Director";
            $code = "MD";
            break;

        case "Revenue Collector":
        case "Operations Manager":
        case "Operations Officer":
        case "Parking Attendant":
        case "Supervisor":
        case "Head of Ticketing":
        case "Monitoring and Compliance":
        case "Monitoring Officer":
        case "Enforcement Officer":
        case "Chief Security Officer":
            $answer = "Operations";
            $code = "OP";
            break;

        case "Chief Operating Officer":
            $answer = "Chief Operating Officer";
            $code = "COO";
            break;

        case "IT, Intern":
        case "Head, IT":
        case "Technical Officer":
            $answer = "IT";
            $code = "IT";
            break;
            
            default :
            $answer = "";
            $code = "";
    }
?>