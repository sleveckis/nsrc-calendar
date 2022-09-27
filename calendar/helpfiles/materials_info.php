<?php
ob_start();
session_start();
$session = session_id();

// For FILE_PATH and ROOT_DIR
include "../config.php";

// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

$_SESSION["max_comment_length"] = 254;

//
// Only display all this info if user is logged in.
//

function materials_info($id, $authed_user)
{

$header_title = "Internet Society (ISOC) Workshop Resource Centre - Summary Materials Information";
$header_heading = "Summary Materials Information";
$header_referrer = "/helpfiles/materials_info.php";
include($_SERVER[DOCUMENT_ROOT].'/include/header.php');
    
?>

<tr><td class="content">

<?php

  $row_ms_materials = db_fetch1("select * from materials where id= ?", array($id));
  
  $title = $row_ms_materials['title'];

echo "<h3>Summary Materials Information</h3>\n";
echo "<b class='bold'>For: </b><font color='#0000ff'>" .$title. "</font></b>\n";

?>

<table border="0" cellpadding="0" cellspacing="0" width="698">

<tr><td colspan='3'>
<br />
Not all fields are required to be filled in. The following information is available for this set of materials:

<p>&nbsp;
</td></tr>

<?php
    echo "<tr><td width='20'></td>\n";

    echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#176b17'><b>Title:</b></font></font></font></td>\n";
    echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#176b17'><b>" .$title. "</b></font></font></font>\n";
    echo "</td></tr>\n";
    
    //                        
    // Display cited authors.
    //
    
    
            echo "<tr><td width='20'></td>\n";

            echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Author(s):</b></font></font></td>\n";
            echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['author']. "</font></font></td>\n";
            echo "</td></tr>\n";
            
            

    
    $materials_id = $row_ms_materials['id'];
    $ws_id = $row_ms_materials['workshop_id'];

    if(isset($authed_user))
        {
            //
            // Note, make sure /helpfiles/user_info.php does a check for login to that
            // savvy users cannot spoof this to get user info without an account.
            //
            echo "<script>\n";
            echo "function openuserinfo" .$x. "(){\n";
            echo "var popurl=\"/helpfiles/user_info.php?id=" .$row_ms_materials['user_id']. "\"\n";
            echo "winpops=window.open(popurl,\"\",\"width=750,height=700,scrollbars,\")\n";
            echo "}\n";
            echo "</script>\n";                                    
            
            echo "<tr><td width='20'></td>\n";
            echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Submitted by:</b></font></font></td>\n";
            echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><a href='javascript:openuserinfo" .$x. "()'>" .$row_ms_materials['submitter']. "</a></font></font>\n";
            echo "</td></tr>\n";
        }

        if((!empty($row_ms_materials['update_author'])) &&
            (isset($authed_user)))
            {
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Updated  by:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['update_author']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if($ws_id != 0)
            {
                $row_ws = db_fetch1("select * from workshop where id= ?", array($ws_id));
                
                $ws_title = $row_ws['title'];
                $ws_city = $row_ws['city'];
                $ws_country = $row_ws['country'];
                $ws_month = $row_ws['month'];
                $ws_year = $row_ws['year'];
                
                echo "<script>\n";
                echo "function openworkshopinfo" .$x. "(){\n";
                echo "var popurl=\"/helpfiles/workshop_info.php?id=" .$ws_id. "\"\n";
                echo "winpops=window.open(popurl,\"\",\"width=750,height=540,scrollbars,\")\n";
                echo "}\n";
                echo "</script>\n";
                
                echo "<tr><td width='20' valign='top'></td>\n";
                echo "<td width='110' valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Workshop:</b></font></font></td>\n";
                echo "<td valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>These materials are part of the workshop: \"<a href='/helpfiles/workshop_info.php?id=$ws_id' target='_blank'>" .$ws_title. "</a>\" - that took place in " .$ws_city. ", " .country_lookup($ws_country). " during " .$ws_month. " of " .$ws_year. ".</font></font>\n";
                echo "</td></tr>\n";  
            }
            
        if(!empty($row_ms_materials['language']))
            {
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Language:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['language']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if(!empty($row_ms_materials['other_language']))
            {
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Other language:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['other_language']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if($row_ms_materials['primary_topic'] != 'null')
            {                                        
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Primary topic:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['primary_topic']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if($row_ms_materials['secondary_topic'] != 'null')
            {                                        
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Secondary topic:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['secondary_topic']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if($row_ms_materials['terciary_topic'] != 'null')
            {                                        
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Tertiary topic:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['terciary_topic']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if(!empty($row_ms_materials['other_topic']))
            {                      
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Other topic:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['other_topic']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
            
        if(!empty($row_ms_materials['month']))
            {                      
                echo "<tr><td width='20' valign='top'></td>\n";
                echo "<td width='110' valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Month:</b></font></font></td>\n";
                echo "<td valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['month']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if(!empty($row_ms_materials['year']))
            {                      
                echo "<tr><td width='20' valign='top'></td>\n";
                echo "<td width='110' valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Year:</b></font></font></td>\n";
                echo "<td valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['year']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        if(!empty($row_ms_materials['last_update']))
            {
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Last updated:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['last_update']. "</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        //
        // Moved this code up here as we need it for the directory
        // listing table item.
        //
            
        $row_files = db_fetch1("select * from materials where id= ?", array($materials_id));
        
        $year = $row_files['year'];
        $unique_dir = $row_files['unique_dir'];
        $license = $row_files['license'];
        $title = $row_files['title'];
        
        $file_dir = "/data/".$year."/".$unique_dir."/";
            
        if(isset($authed_user))
            {
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Directory:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>Click <a href='" .$file_dir. "' target='_blank'>here</a> to see a directory listing of files in a separate window</font></font>\n";
                echo "</td></tr>\n";                    
            }
            
        $license = $row_ms_materials['license'];
            
        if($license != 'no-license')    
            {
                $row_material_license = db_fetch1("select * from licenses where short_name= ?", array($license));
                    
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Licensed as:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_material_license['descriptor']. " [ <a href='" .$row_material_license['summary_url']. "' target='_blank'>summary</a> | <a href='" .$row_material_license['fulltext_url']. "' target='_blank'>full text</a> ]</font></font>\n";
                echo "</td></tr>\n";
                
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>Remember that <i>you must</i> read and agree to the terms of this license to use these materials!</font></font>\n";
                echo "</td></tr>\n";
            }
        else
            {
                echo "<tr><td width='20'></td>\n";
                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Licensed as:</b></font></font></td>\n";
                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'>No License:</font> No licensing required (No Attribution necessary, any use allowable).</font></font>\n";
                echo "</td></tr>\n";
                
            } // end license if/else clause
           

        if(!empty($row_ms_materials['comment']))
            {
                $material_comment = html_entity_decode(stripslashes($row_ms_materials["comment"]));
                // $material_comment = stripslashes($row_ms_materials['comment']);
                echo "<tr><td width='20' valign='top'><br /></td>\n";
                echo "<td width='110' valign='top'><br /><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Comments:</b></font></font></td>\n";
                echo "<td valign='top'><br /><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>";
                echo "<textarea rows='20' name='text' cols='90' readonly>".$material_comment."</textarea>";
                echo "</font></font>\n";
                echo "</td></tr>\n";
            }

                echo "<tr><td colspan='3'>\n";
                echo "&nbsp;\n";
                echo "</td></tr>\n";
            
            
        echo "</table>\n";
        //
        // Now for the fun. Drop in the individual item code for each set of materials
        // right here.
        //

?>

        <table width="699" border="1" cellspacing="0" cellpadding="0" bgcolor='#e3e3e3'>
        <tr><td align="left" width="30%">
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <b>Filename</b>*
        </font></font>
        
        </td><td align="left" width="20%">
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <b>Filetype</b>
        </font></font>
        
        </td><td align="left" width="10%">
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <b>Size</b>
        </font></font>
        
        </td><td align="left" width="40%">
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <b>Comment</b>
        </font></font>
        </td></tr>
        
<?php    
	    $not_logged_in_comment = 'no';
            
            $result_items = db_exec("select * from materials_item where materials_id= ?", array($materials_id));
            while ($row_items = $result_items->fetch())
                {
        
                $files_dir = "/data/".$year."/".$row_items['unique_dir']."/";
                $filename = $row_items['filename'];
                $file_url = $files_dir.$filename;
                    
                echo "<tr><td width='30%' align='left' valign='top'>\n";
                echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                echo "<a href='$file_url' target='_blank'>".$filename."</a>\n";
                echo "</font></font>\n";
                
                echo "</td><td width='20%' align='left' valign='top'>\n";
                echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                echo $row_items['mimetype'];
                echo "</font></font>\n";
                
                echo "</td><td width='10%' align='left' valign='top'>\n";
                echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                
                if($row_items['filesize'] > 1024*1024)
                    {
                        $file_size = (($row_items['filesize']) / (1024*1024));
                        $file_size = number_format($file_size, 2);
                        $file_size = "" .$file_size. " MB\n";
                    }
                    else
                    {
                        $file_size = ($row_items['filesize']) / 1024;
                        $file_size = number_format($file_size, 2);
                        $file_size = "" .$file_size. " K\n";
                    }
                
                echo $file_size;
                echo "</font></font>\n";
                
                echo "</td><td width='40%' align='left' valign='top'>\n";
                echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                
                    //
                    // If user logged in and there's a comment let them see it,
                    // elseif not logged in and a comment note there is one, but
                    // don't link to it, else no comment.
                    //                                    

                                         if((!empty($row_items["comment"])) &&
                                            (isset($authed_user)))
                                            {
                                                $comment = $row_items["comment"];
                                                $comment_length = strlen($comment);
                                                
                                                if($comment_length > $_SESSION["max_comment_length"])
                                                    {
						      $comment = substr($comment, 0, $_SESSION["max_comment_length"]);
                                                    }
                                                    
                                                $count = $row_items["id"];
						// echo "<a href='/helpfiles/file_comments.php?counter=" .$count. "' target='_blank' alt='$comment'>$comment</a>\n";
                                                echo stripslashes($comment);
                        }
                        elseif((!empty($row_items["comment"])) &&
                        (!isset($authed_user)))
                        {
                            echo "Yes**\n";
			    $not_logged_in_comment = 'yes';
                        }
                        else
                        {
                            echo "None\n";
                        }
                        
                echo "</font></font>\n";
                
                echo "</td></tr>\n";
                }
        
                echo "</table>\n";


echo "*Right click filenames to download files. Not all filetypes are visible in your browser.\n";
   if($not_logged_in_comment == 'yes')
     {
       echo "<br><b>**Not logged in.</b> Comments are not displayed.\n";
     }
echo "</font></font>\n";

 echo "<br>&nbsp;\n";

echo "<table width='700' border='0'>\n";
echo "<tr><td>\n";
echo "<center><form><input type=button value=' Close Window ' onClick='self.close();'></form></center>\n";
echo "</td><tr>\n";
echo "</table>\n";

 echo "</td></tr>\n";                
 echo "</table>\n";


// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.

    //$our_filename = $_SERVER[DOCUMENT_ROOT]."/helpfiles/materials_info.php";
    include FILE_PATH . "/include/footer.php";

echo "</body>\n";
echo "</html>\n";

} // end function materials_info

?>

<?php
//
// Main
// 

if (session_is_registered("authenticated_user"))
      {
	$authed_user = $GLOBALS["authenticated_user"];

	$row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
	$user_name = $row_name['name'];
	
	materials_info($id, $authed_user);
      }
elseif(!session_is_registered("authenticated_user"))
      {
	materials_info($id,'');
      }

?>
