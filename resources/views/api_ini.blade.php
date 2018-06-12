<?php
    if(isset($filename)){
        if(file_exists($filename)){
            if(!isset($class)){$class = "";} else {$class = ' CLASS="' . $class . '"';}
            $file = explode("\r\n", file_get_contents($filename));
            $currentsection = false;
            foreach($file as $line){
                $line = trim($line);
                if($line){
                    if(startswith($line, "[") && endswith($line, "]")){
                        if($currentsection){
                            echo '>' . $currentsection . '</DIV>';
                        }
                        $currentsection = mid($line, 1, strlen($line) - 2);
                        echo '<DIV ONCLICK="' . $onclick . '(this);"' . $class;
                    } else if (!startswith($line, "#") && textcontains($line, "=")){
                        $line = " " . str_replace("=", '="', $line) . '"';
                        echo $line;
                    }
                }
            }
            if($currentsection){echo '>' . $currentsection . '</DIV>';}
        } else {
            echo $filename . ' not found';
        }
    } else {
        echo 'No file specified';
    }
?>