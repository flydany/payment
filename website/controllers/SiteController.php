<?php

namespace website\controllers;

use Yii;

class SiteController extends Controller {
    
    public function actionTest()
    {

    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->v(base64_encode('MIIEpAIBAAKCAQEAgZtJ4RaLiqRjDusvn6V2qHQ7yjsXENMWERLmaAMoShOXBNLafpST2xQNvqnPZxCjtdC4wdrtpsXXLJEHSsqrMtNM1fvXY7Fcf8THU8ozFdSUQ/+1iQ1oK/Ixs9DiY7Hrr0768TAqPPpl0lFeZm/08/8/3Pgmo8IWCyLpX+UgFOJvgm1hR7Emt+M3Qf9x3A6lw443FcekgFuHoiKXowMKsvX1pVVEb2W9XuuI6F0n/Pt9a5dJ+ifXYR/hv4RJo1ZAkDkBTg3K++NoyHNz9Fu8T1QXWKkWmBaJ+4ebh7vON2OGqE8v7ms7nByXPpbVw+3N+5xfkYBbqZYhhkhNrZf5LwIDAQABAoIBAHF7LdIHMoFfdFiwts87Ss0ukNd+q7CQkua6PMHf2dwakQWpNaTVpSySv4ItHyuoFx/wNIWQkruOyNv91Hu5PvNOrh1C2JIyzsIU407LYbWAn512fVYWRhsMglZm1ILkW2/xYKnkOWeQ+6yOkHBzIPCvkxSAtxx1qWVw2RSdA10krqz6DDt8e7N/Dt0Ih9QxtcgvGNbBpUujKPGwX0RUDd1vBqilDCoB/FwZddVUh9dSQrBA/h2HSF6AUMJXr/1UQUERYO9yJC4w3DcPWiJ+e0bTPDrTcTB1Aocx2ESVZZWNe+efG4DXMcMsfFtKqEySsVROhgU9Pj8CR6ke+NYjHmkCgYEA2mQjOfS3Qf6cuAoEQt7PiJwLcf//26q+MndZLFAfZzYOK6SgPZUvvf6tR02JYlGfDBDfF8t1QBLb6rlOOLpzY+ocDO3ebtQDbXozvxtD2pTUlJxcyp7O1P1cF8rrA1o1VDY2YClpbohVRePLOKWUAJT7ob2sK2Jua7r30XuxPJ0CgYEAl+0ML5akJt3Iankz8vpLlUvbb+g04TWeYgi4IdAHxPnyJKpw9BDY4piVy5VUUDyMMQed4umeKdYkqwhndaUK90kbSBeGNXY7b32KQMoMFTALmkgg5cxT4JrqFXU9oyZwOLaZcTs9j7KdqklHykCD3Qvnj/rwgaZp88gQU/w0tTsCgYEA0A3kLVuAlqOc5AvO4XHpHskicjmjo783MVbYIko2kJpDx7ovWlvdPtD4fVxVzM/biKAuGTogeqk8z5I3/f1K3t3yWTLn3IeouExaLe8opn1xRB7um63Nd8XjTVtopynyckavaM8q/T1ul4WbXl0H8cH9M6pB8pb0gTd+zhhb0lUCgYAH7ZrhMN9IF/LJe6G2YFFpbRf6cwevaPjm7MWPHigJo3F7cFmMowRubsACa7yGo2I9fMREfyR90mr/ceTUQtSbqvj9fhzG1XCslMSONP0Ebgls966fz1XhNrRYglHBHdRlYUIIoqrHwO5xYHmamFtJQcHHxbSfT2vgCOSxPUwS2QKBgQCGl6MvAAlc1u79VrV/n9aaaPHaVxi8NotFdR48sAVcl7cl1+pzh11zsjAcBH6k7ouMBgy77J2ZfVQ134CAwxnZiHm5Jtn51fgk31z+dKTc3HZhXmIoVIaKSko3f//YvN6St+pqLDp/8W+zr/Q+p0NcUzwftZiUpqmNjYO69K+jpQ=='));
        $this->v(base64_encode('MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB'));

        return;
        // $this->layout = 'simple.php';

        return $this->render('index');
    }
}
