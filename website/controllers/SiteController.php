<?php

namespace website\controllers;

use Yii;

class SiteController extends Controller {
    
    public function actionDes()
    {
        $key = '5old71wihg2tqjug9kkpxnhx9hiujoqj';
        $this->v('f834b9874c6b489e665d2fe57faf54f9');
        return $this->v(md5('|0001|失败||'));
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<REQUEST><VERSION>1.0</VERSION><MCHNTCD>0002900F0096235</MCHNTCD><USERIP>118.31.50.91</USERIP><MCHNTORDERID>jjqwgasioscax0tk</MCHNTORDERID><USERID>204</USERID><AMT>1</AMT><PROTOCOLNO>TWAK9T100000013872NXIY</PROTOCOLNO><BACKURL>http://test.debit.koudailc.com:8080/agreement-api/agreement-notify/fuiou?a=0002900F0096235</BACKURL><REM1/><REM2/><REM3/><TYPE>03</TYPE><NEEDSENDMSG>0</NEEDSENDMSG><SIGNTP>md5</SIGNTP><SIGN>ada53d1bdc9364f6929bcd5eaf8804ff</SIGN></REQUEST>
';
        // $this->v(openssl_get_cipher_methods());
        $signurate = openssl_encrypt($xml, 'des-ecb', $key);
        $right = 'qkpKyYyHiphK5QsmwdqcVhE8CxNl+BDTFCo2F7fV5H3kea4al1Es9QLiLileOa5Sisp8p7LaTSJtfxqewkQSH7ihqpX8t/EZiAZhOSqW1vqDVZjrqko0kENi3uBopmKasK0NjC6PWLE11Fg0/GODKkXowsMu49JRBSmr3R3PRs8ZunUzC07kYMq7rP/qqGgsbR4od9TaJRDu8OOmRSrFmtYnwgBtdsq2W/4TbroLT3l0u344gQTTshBuE1bkvmOyGxzs7RJ0ECcATuB6q36E4KvpzvEefN0M3FzGBf60+/WPLWMTrBvY9gKNtRn5rLO2mXBUCyVAyDDx569pg5jep26XlP0gafVXx7esUccmbV0viYBU6HdeC7PsBFc68++ZO3rBLToqjy8sUQDoZL+2FGssJ4OaqZSDK+JFnWpWmi0PsBVrbdcbRyNGg6nH/fSOQf1vcN3tMwdnWCCS81ivnEacplYRuQdYmKEvk2+l+IqdPC4OHtIGoAz6cXbws0makiI6dyRimBc2uQ/fFF6FOjbIdsfiJGN50wwM+ueFFdQOC1OJNRwOu3w2nMWtM5puB1z6mpMojB6Qu5nLDJ2POsDyCTEyuZ3zHnfYRs5uxrf5lsBvUCQdpzrh6+suehsYx3GEF6/39MiVUJvO3t2KZ3wSGdgx6DkdQFGsaYFwOiQc3HIZvA4VQ+kpURDaji8n';
        $this->v(openssl_decrypt($right, 'des-ecb', $key));
        $this->v($signurate == $right ? 'true' : 'false');
        return $this->v($signurate);
    }

    public function actionTest()
    {
        
        return $this->render('test');
    }

    /**
     * Displays homepage.
     * @return string
     */
    public function actionIndex()
    {
        $this->v(base64_encode('MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIbA52JWbirSYa2iTd/P7 G6NGgOAAmgGFcTaktRVhHtgeeTHd24iT2MNTCIw/3ykcWu/55hbpHBcZIiLf/XZ940iae SgIGmfoJa9xdVmZ5l4ElPUVtLJMntUfbPdAMP8SEwjMP8Nr6PvzjcKXS5GCfCuTW/F/dK z1mR1LOcxAkLBAgMBAAECgYBjkzBoLk4CPqwHTqQU+uRPXN0YMQOWMsjrSkittvPK56Or Nuo97ASVwUG9Ek/4ntthL9HHeBCvJtbzP4Iy/fo6sevZVcaURNb3mn1R/gdIitwFur8bd F+VA5mZX8cTR4D4liZZvBHwx+UtvdWClzoOSeSpFZn7/6nMXpYzam3WQQJBALvXIHeAdP rtktmRtqmdVNYGqmgtE7jqkaqZ9VgUMcIt8W01oPEDp27NtmGTM06nneIk/ajagq97nsb c6JPa6PUCQQC3pm9RM782qnL/5fzNsv7HyTjFAlIg3Q+PNlSj1d3ekNlqRJ0hv4/aLiqr LqtqbfHu98aeGt4JsdilT/Z9rwMdAkEAlFgwFtBHEkh/Wf3ewRM0hZZcC8vVsIrnoVDXV skUBuNbsEDTKqQVHceuSl8C/RIY+Rj3jtuKq+W4HhsmPmZ65QJAbtbypG244ENreOrT80 ou32Gg87Z83vzMoUDHQMKZT/TYY3zZ4T5+kc3/TqWyK2AD/photY+9pthByzRBroVsOQJ AMUQ/c+Mngb8kKxU+mF/CwDSlwbL8/lM/xoDnT/qQxmxTiEysohd3jO98C0BA3+YHFswa XKtY7/Tp/H1VeX9EdA=='));
        $this->v(base64_encode('MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCBCcvUDkw3ONsVx7Rzh9IJoKKurwBnK SjJEJbLXQWDKIPZMtmxcHa5jNu6OgpQ0BatOYl4p4BmgH3HzVwWyn6iDOsDlxwZezFzAr tPjtECq241nfmoGhbz9lMr7T56yY5PhATws32Dm1ZQbY8DvsFvTe2hKgmIGbZQ030seRn fSwIDAQAB'));

        return;
        // $this->layout = 'simple.php';

        return $this->render('index');
    }

    public function actionCodes()
    {
        $codes = [
            '1001', 
            '1004', 
            '1005', 
            '1008', 
            '3001', 
            '4002', 
            '4003', 
            '4004', 
            '4005', 
            '4006', 
            '4007', 
            '4008', 
            '4009', 
            '4010', 
            '4011', 
            '4012', 
            '4013', 
            '4014', 
            '4015', 
            '9104', 
            '9910', 
            '9911', 
            '9912', 
            '9913', 
        ];
        $len = 0;
        foreach($codes as $code) {
            if($len) {
                echo ' ';
            }
            echo "'{$code}',";
            $len += strlen($code);
            if($len >= 100) {
                echo '<br>';
                $len = 0;
            }
        }
    }
}
