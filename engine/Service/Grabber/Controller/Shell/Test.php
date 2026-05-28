<?php

namespace Service\Grabber;


class Controller_Shell_Test extends Controller_Shell
{
    public function A_Karma()
    {
        $tables = [
            'users_karma_2019_10',
            'users_karma_2019_09',
            'users_karma_2019_08',
            'users_karma_2019_07',
            'users_karma_2019_06',
            'users_karma_2019_05',
            'users_karma_2019_04',
            'users_karma_2019_03',
        ];
        $userIds = [];

        foreach ($tables as $table) {
            $sql = 'SELECT `userId`, MAX(`karmaTo`) as `karmaTo` FROM `' . $table . '` GROUP BY `userId`';
            $res = $this->factoryGrabber->db->query($sql);

            $count = 0;

            while ($row = $res->fetch_assoc()) {
                if (isset($userIds[$row['userId']])) {
                    continue;
                }
                $count++;

                $this->factoryGrabber->db->query('UPDATE `users` SET `karma` = ' . $row['karmaTo'] . ' WHERE `userId` = ' . $row['userId']);
                $userIds[$row['userId']] = true;
            }
        }
    }

    public function A_Test()
    {
        $page = 0;
        do {
            $count = 0;
            $query = $this->factoryGrabber->groups->query()->limit(1000)->offset($page);
            $query->filter->fieldValue('title', '=', 'Мемасы');
            $query->filter->fieldValue('ownerId', '!=', '79561215');
            $it = $query->iteratorForSave();
            /** @var Model_Groups_Group $group */
            foreach ($it as $group) {
                $count++;

                $access_token = 'a1878692a6e47a005a6f701edc2282c64f2b72ec185f75f08535cd4735b479eec3cdee716cc20c788992d';
                $response = $this->VK->getGroup($group->ownerId, $access_token);

                if (isset($response[0]['id']) && $response[0]['id'] == $group->ownerId) {
                    $group->title = $response[0]['name'];
                    $group->url = 'https://vk.com/' . $response[0]['screen_name'];
                    $group->photo = $response[0]['photo_100'];
                    $this->factoryGrabber->groups->save($group);
                }
                usleep(200000);
                print_r($response);
            }
            $page++;
        } while ($count);
    }

    private function _getPhotoDataText()
    {
        $image = new \Imagick();
        $image->readImage(IMAGES_PATH . 'grabber/test/test.jpg');
        $watermark = new \Imagick();
        $watermark->readImage(IMAGES_PATH . 'grabber/watermark/1544613252217_new.png');
        $watermark->setImageFormat('png');
        $watermark = $watermark->flattenImages();

        $w = min($watermark->getImageWidth(), $image->getImageWidth());
        $h = min($watermark->getImageHeight(), $image->getImageHeight());

        $src_ratio = $watermark->getImageWidth() / $watermark->getImageHeight();
        $new_w = $w;
        $new_h = $h;
        $ratio = $w / $h;

        if ($ratio < $src_ratio) {
            $new_h = $new_w / $src_ratio;
        } else {
            $new_w = $new_h * $src_ratio;
        }

        $watermark->scaleImage($new_w, $new_h);

        $x = 0;
        $y = 0;

        switch (0) {
            case 0:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 1:
                $x = $image->getImageWidth() - $watermark->getImageWidth();
                $y = $image->getImageHeight() - $watermark->getImageHeight();
                break;
            case 2:
                $x = 0;
                $y = $image->getImageHeight() - $watermark->getImageHeight();
                break;
            case 3:
                $x = 0;
                $y = 0;
                break;
            case 4:
                $x = $image->getImageWidth() - $watermark->getImageWidth();
                $y = 0;
                break;
            case 5:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = 0;
                break;
            case 6:
                $x = 0;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 7:
                $x = $image->getImageWidth() - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight() / 2 - $watermark->getImageHeight() / 2;
                break;
            case 8:
                $x = $image->getImageWidth() / 2 - $watermark->getImageWidth() / 2;
                $y = $image->getImageHeight();
        }

        $image->compositeImage($watermark, \Imagick::COMPOSITE_MULTIPLY, $x, $y);
        $image->setImageFormat('jpg');
        $image->writeImage(IMAGES_PATH . 'grabber/test/output.jpg');
    }
}
