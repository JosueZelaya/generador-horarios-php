<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        class A {
            public $foo = 1;
        }  

        $a = new A;
        $b = $a;     // $a and $b are copies of the same identifier
                     // ($a) = ($b) = <id>
        $b->foo = 2;
        echo $a->foo."\n";


        $c = new A;
        $d = &$c;    // $c and $d are references
                     // ($c,$d) = <id>

        $d->foo = 4;
        echo $c->foo."\n";


        $e = new A;
        
        function foo($obj) {
            // ($obj) = ($e) = <id>
            $obj->foo = 6;
        }

        foo($e);
        echo $e->foo."\n";
        
        echo "<br/>";
        echo $a->foo."<br/>";
        echo $b->foo."<br/>";
        echo $c->foo."<br/>";
        echo $d->foo."<br/>";
        echo $e->foo."<br/>";        
        ?>
    </body>
</html>
