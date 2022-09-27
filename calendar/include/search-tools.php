<?php

session_start();
$session = session_id();

//
// As many of the search pages have the sampe output for all searches that return
// results, but just have different search criteria they need to figure out it
// seems like it would be easier to put the search outputs in one file and the
// search result code in the main search file.
//

function search_materials($search_materials_string)
    {

if($view_all == 'TRUE')
    {
    echo "<b><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'>Query 'View all materials':</b></font></font></font>\n";
              $x = 0;
            $result_ms_materials = db_exec("select * from materials where approved='Y' order by year", array());
            while ($row_ms_materials = $result_ms_materials->fetch())
                   {
                       if(($x == 0) &&
                        (!empty($row_ms_materials['title'])))
                        {
                        echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'> (only fields that were filled in by the submitter are shown below)</font></font></font><p>\n";
                        }
                        
                       if($x > 0)
                        {
                            echo "<br>\n";
                        }
                       $x++;
            
                        echo "<table width='671' border='0'>\n";
                        echo "<tr><td width='20'></td>\n";
            
                        echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#176b17'><b>Title:</b></font></font></font>\n";
                        echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#176b17'><b>" .$row_ms_materials['title']. "</b></font></font></font>\n";
                        echo "</td></tr>\n";
                        
                        //                        
                        // Display cited authors if user is logged in.
                        //
                        
                        if(isset($authed_user))
                            {                        
                        
                                echo "<tr><td width='20'></td>\n";
                    
                                echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Author(s):</b></font></font>\n";
                                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['author']. "</font></font>\n";
                                echo "</td></tr>\n";
                            }
                        
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
                                
                                $ms_mat_userid = $row_ms_materials['user_id'];
                                
                                echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><a href='/helpfiles/user_info.php?id=$ms_mat_userid' target='_blank'>" .$row_ms_materials['submitter']. "</a></font></font>\n";
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
                                    $topic_id = $row_ms_materials['primary_topic'];
                                    
                                    $row_ms_topic = db_fetch1("select * from topics where id= ?", array($topic_id));
                                    
                                    echo "<tr><td width='20'></td>\n";
                                    echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Primary topic:</b></font></font></td>\n";
                                    echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_topic['topic']. "</font></font>\n";
                                    echo "</td></tr>\n";                    
                                }
                                
                            if($row_ms_materials['secondary_topic'] != 'null')
                                {
                                    $topic_id = $row_ms_materials['secondary_topic'];
                                    
                                    $row_ms_topic = db_fetch1("select * from topics where id= ?", array($topic_id));
                                    
                                    echo "<tr><td width='20'></td>\n";
                                    echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Secondary topic:</b></font></font></td>\n";
                                    echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_topic['topic']. "</font></font>\n";
                                    echo "</td></tr>\n";                    
                                }
                                
                            if($row_ms_materials['terciary_topic'] != 'null')
                                {
                                    $topic_id = $row_ms_materials['terciary_topic'];
                                    
                                    $row_ms_topic = db_fetch1("select * from topics where id= ?", array($topic_id));
                                    
                                    echo "<tr><td width='20'></td>\n";
                                    echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Terciary topic:</b></font></font></td>\n";
                                    echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_topic['topic']. "</font></font>\n";
                                    echo "</td></tr>\n";                    
                                }
                                
                            if(!empty($row_ms_materials['other_topic']))
                                {                      
                                    echo "<tr><td width='20'></td>\n";
                                    echo "<td width='110'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Other topic:</b></font></font></td>\n";
                                    echo "<td><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['other_topic']. "</font></font>\n";
                                    echo "</td></tr>\n";                    
                                }
                                
                            if(!empty($row_ms_materials['comment']))
                                {                      
                                    echo "<tr><td width='20' valign='top'></td>\n";
                                    echo "<td width='110' valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><b>Comments:</b></font></font></td>\n";
                                    echo "<td valign='top'><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>" .$row_ms_materials['comment']. "</font></font>\n";
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
                                
                            echo "</table>\n";
                            
                            //
                            // Now for the fun. Drop in the individual item code for each set of materials
                            // right here.
                            //
    
?>

                            <table width="743" border="0" cellspacing="0" cellpadding="0">
                            <tr><td width="26">&nbsp;</td>
                            <td width="717">

                            <table width="716" border="1" cellspacing="0" cellpadding="0">
                            <tr><td align="left" width="50%">
                            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
                            <b>Filename</b>*
                            </font></font>
                            
                            </td><td align="left" width="20%">
                            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
                            <b>Filetype</b>
                            </font></font>
                            
                            </td><td align="left" width="20%">
                            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
                            <b>Size</b>
                            </font></font>
                            
                            </td><td align="left" width="10%">
                            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
                            <b>Comment</b>
                            </font></font>
                            </td></tr>
                            
<?php    
                                $result_items = db_exec("select * from materials_item where materials_id= ?", array($materials_id));
                                while ($row_items = $result_items->fetch())
                                    {
                            
                                    $files_dir = "/data/".$year."/".$row_items['unique_dir']."/";
                                    $filename = $row_items['filename'];
                                    $file_url = $files_dir.$filename;
                                        
                                    echo "<tr><td width='50%' align='left'>\n";
                                    echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                                    echo "<a href='$file_url'>".$filename."</a>\n";
                                    echo "</font></font>\n";
                                    
                                    echo "</td><td width='20%' align='left'>\n";
                                    echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                                    echo $row_items['mimetype'];
                                    echo "</font></font>\n";
                                    
                                    echo "</td><td width='20%' align='left'>\n";
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
                                    
                                    echo "</td><td width='10%' align='left'>\n";
                                    echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                                    
                                        //
                                        // If user logged in and there's a comment let them see it,
                                        // elseif not logged in and a comment note there is one, but
                                        // don't link to it, else no comment.
                                        //                                    
                                    
                                         if((!empty($row_items["comment"])) &&
                                            (isset($authed_user)))
                                            {
                                                $count = $row_items["id"];
                                                echo "<script>\n";
                                                echo "function openfilecomment" .$count. "(){\n";
                                                echo "var popurl=\"/helpfiles/file_comments.php?counter=" .$count. "\"\n";
                                                echo "winpops=window.open(popurl,\"\",\"width=620,height=300,scrollbars,\")\n";
                                                echo "}\n";
                                                echo "</script>\n";
                                                echo "<a href='/helpfiles/file_comments.php?counter=$count' target='_blank'>Yes</a>\n";
                                            }
                                         elseif((!empty($row_items["comment"])) &&
                                            (!isset($authed_user)))
                                            {
                                                echo "Yes\n";
                                            }
                                           else
                                            {
                                             echo "None\n";
                                            }
                                            
                                    echo "</font></font>\n";
                                    
                                    echo "</td></tr>\n";
                                    }
                            
                                    echo "</td></tr>\n";
                                    echo "</table>\n";
                                    
                                    echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='1'>\n";
                                    echo "*Right click filenames to download files.\n";
                                    echo "</font></font>\n";
                                    
                                    echo "</table>\n";
                
                } // end if
        } // end inner while
//  } // end outer while loop

//} // end if 'view_all == TRUE' and show_both or show_materials is on

 } // end search_materials

?>
