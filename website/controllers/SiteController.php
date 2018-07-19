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
            '0001', '0003', '0020', '0030', '100000', '100004', '100005', '100010', '100011', '100012', '100013', '100014', '100015', '100016', '100017', '100018', '100019', '100020', '100021', '100029', '10002A', '10002B', '100030', '100039', '100040', '100041', '100042', '100043', '100044', '100045', '100046', '100048', '100049', '100050', '10013', '1002', '10029', '1003', '1004', '1005', '1007', '1008', '1010', '1011', '1014', '1029', '1030', '1031', '1033', '1034', '1040', '1042', '1051', '1055', '1059', '1061', '1063', '1075', '10CE', '10EC', '10ET', '10F6', '10FC', '10FD', '10FE', '10L3', '10M0', '10M1', '10M2', '10SM', '10XC', '11V3', '11T3', '11L3', '11M3', '11D3', '11E3', '12S5', '144K', '145K', '16Y4', '17Y4', '200001', '200002', '200012', '200013', '200014', '200015', '200016', '200017', '200023', '200029', '200098', '2001', '24PP', '3201', '323P', '324P', '32PP', '320P', '321P', '35CA', '35CB', '35CC', '35CD', '5178', '5188', '5190', '5353', '5505', '5594', '55AC', '5878', '8010', '80AX', '8110', '8210', '9999', '9788', '999992', '999997', '999998'
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
