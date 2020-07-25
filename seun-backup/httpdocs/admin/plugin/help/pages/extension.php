<?php
$engine = new engine();
?>
<br /><br />
You can extend or override the existing methods without affection other usage in the system<br />

All extension can be accessed as an API link or you can include it in your Application<br /><br />

<strong>HOW TO CREAT EXTENTION</strong><br />
<strong>METHOD 1, API</strong><br />
<strong>METHOD 2, EXTEND DOCUMENT</strong><br /><br />

<strong>METHOD 1, API</strong><br />
Example Link https://shuzia.com/api/v1/result/welcome_guest/name/seun.xml<br /><br />

Go to api/source/<br />

Create a folder for your project, the folder is the v1 as appeared above<br />

Then create a .php file inside the folder, the name of this file is your service name, the service name above is result<br />

Sample content of result is found below<br /><br />


<pre>
class result extends Api
{


    public function __construct($method)
    {
        Api::Api_Method("welcome_guest", "GET", $method); // this tell the API to only allow GET method
        //Api::Api_Method("auth_admin", "POST", $method); // this tell the API to only allow POST method
    }


    public function welcome_guest($parameter)
    {
        return "Welcome " . $parameter['name'];
    }


}
</pre>



<br /><br />
The file name and the class name must be the same<br />
Extend the system existing method and configuration by adding <strong>extends Api</strong><br />
You do not need to include any file, as the system will fix it automatically<br /><br />


Now create any method you wish to create here, method <strong>welcome_guest</strong> is created above with $parameter as passed variable.<br /><br />

https://shuzia.com/api/v1/result/welcome_guest/name/seun.xml<br /><br />

The above structure explains further how to access the extension
<br /><br />

HOW TO RUN SQL QUERY<br /><br />
Example 1
<pre>
$row = self::db_query("SELECT student_id FROM student_list WHERE reg_number = ? LIMIT 1",array($reg_number));
$student_id = $row[0]['student_id'];
 </pre>  <br /><br />     
Example 2
<pre>
       $row = self::db_query("SELECT term,year,subject FROM student_result WHERE student_id = ?",array($student_id));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            echo $row[$dbc]['subject'];

        }
</pre>  <br /><br />     
Example 3
<pre>
       $count = self::db_query("SELECT term FROM student_result WHERE student_id = ?",array($student_id),true);
        echo $count;
        </pre><br /><br />
You can also use other method on the extended class by calling self
<br /><br />
For usage visit the <a href="api1">API section</a><br /><br /><br /><br />



<strong>HOW TO BUILD A THEME</strong><br />
Coming Soon<br />
<br /><br /><br />



<strong>METHOD 2, EXTEND DOCUMENT</strong><br />
Include "<strong>engine.autoloader.php</strong>" in your code, the file is located at the root directory<br />
call $engine->method();<br />
example: $engine-> check_student_result(array("pin"=>" 1234","reg_number"=>" NOU164047531","year"=>"2017","term"=>"1"));<br />
The example above shows how to view student result for list of available method visit the <a href="help&p=api">API section</a><br /><br />

<strong>MYSQL QUERRY SAMPLE</strong><br />
Example 1
<pre>
$row = $engine->db_query("SELECT student_id FROM student_list WHERE reg_number = ? LIMIT 1",array($reg_number));
$student_id = $row[0]['student_id'];
 </pre>  <br /><br />     
Example 2
<pre>
       $row = $engine->db_query("SELECT term,year,subject FROM student_result WHERE student_id = ?",array($student_id));
        for ($dbc = 0; $dbc < $engine->array_count($row); $dbc++) {
            echo $row[$dbc]['subject'];

        }
</pre>  <br /><br />     
Example 3
<pre>
       $count = $engine->db_query("SELECT term FROM student_result WHERE student_id = ?",array($student_id),true);
        echo $count;
        </pre><br /><br /><br />