<?php

class Lib_Html
{
    public static function array_to_xml(array $arr, SimpleXMLElement $xml)
    {
        foreach ($arr as $k => $v) {
            is_array($v)
                ? \Lib_Html::array_to_xml($v, $xml->addChild($k))
                : $xml->addChild($k, $v);
        }

        return $xml;
    }

    public static $mediaPattern = [
        [
            'pattern' => 'http://smotri.com/video/view/\?id=([\d\w]+)',
            'params' => ['id' => 1],
            'data' => '<object width="640" height="360"><param name="movie" value="http://pics.smotri.com/player.swf?file={id}&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="bgcolor" value="#ffffff" /><embed src="http://pics.smotri.com/player.swf?file={id}&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque"  width="640" height="360" type="application/x-shockwave-flash"></embed></object>',
        ],
        [
            'pattern' => 'http://pics.smotri.com/player.swf\?file=([\d\w]+)',
            'params' => ['id' => 1],
            'data' => '<object width="640" height="360"><param name="movie" value="http://pics.smotri.com/player.swf?file={id}&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="bgcolor" value="#ffffff" /><embed src="http://pics.smotri.com/player.swf?file={id}&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque"  width="640" height="360" type="application/x-shockwave-flash"></embed></object>',
        ],
        [
            'pattern' => 'http://www.youtube.com/watch\?v=([\d\w\-_]+)',
            'params' => ['id' => 1],
            'data' => '<iframe width="640" height="360" src="http://www.youtube.com/embed/{id}?rel=0" frameborder="0" allowfullscreen="true"></iframe>',
        ],
        [
            'pattern' => 'http://youtu\.be/([\d\w\-_]+)',
            'params' => ['id' => 1],
            'data' => '<iframe width="640" height="360" src="http://www.youtube.com/embed/{id}?rel=0" frameborder="0" allowfullscreen="true"></iframe>',
        ],
        [
            'pattern' => '//(?:www\.)?youtube\.com/embed/([\d\w\-_]+)',
            'params' => ['id' => 1],
            'data' => '<iframe width="640" height="360" src="http://www.youtube.com/embed/{id}?rel=0" frameborder="0" allowfullscreen="true"></iframe>',
        ],
        [
            'pattern' => '//www.youtube.com/v/([\d\w\-_]+)',
            'params' => ['id' => 1],
            'data' => '<iframe width="640" height="360" src="http://www.youtube.com/embed/{id}?rel=0" frameborder="0" allowfullscreen="true"></iframe>',
        ],
        [
            'pattern' => 'http://rutube\.ru/video/embed/([\d]+)',
            'params' => ['id' => 1],
            'data' => '<iframe width="640" height="360" src="http://rutube.ru/video/embed/{id}" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowfullscreen="true" scrolling="no"></iframe>',
        ],
        [
            'pattern' => 'http://rutube\.ru/tracks/([\d]+)\.html?.*v=([\w\-_]+)',
            'params' => ['hash' => 1, 'id' => 2],
            'data' => '<iframe width="640" height="360" src="http://rutube.ru/video/embed/{hash}" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowfullscreen="true" scrolling="no"></iframe>',
        ],
        [
            'pattern' => 'http://video\.mail\.ru/(.+).html',
            'params' => ['id' => 1],
            'data' => '<object width="640" height="360" type="application/x-shockwave-flash" data="http://img.mail.ru/r/video2/player_v2.swf?2"><param name="movie" value="http://img.mail.ru/r/video2/player_v2.swf?2" /><param name="flashvars" value="movieSrc={id}" /><param name="devicefont" value="false"/><param name="menu" value="false"/><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /></object>',
        ],
        [
            'pattern' => 'http://(?:www\.)?1tv\.ru/.+?/(\d+)',
            'params' => ['id' => 1],
            'data' => '<object width="640" height="360"><embed width="640" height="360" align="middle" flashvars="stats=http://www.1tv.ru/addclick/" allowscriptaccess="always" wmode="opaque" allowfullscreen="true" quality="high" src="http://www.1tv.ru/newsvideo/{id}" type="application/x-shockwave-flash"/></object>',
        ],
        [
            'pattern' => 'http://player\.rutv\.ru/(.+?\?acc_video_id=\d+)',
            'params' => ['id' => 1],
            'data' => '<iframe src="http://player.rutv.ru/{id}" frameborder="0" style="width: 640px; height: 360px; border: none;"></iframe>',
        ],
        [
            'pattern' => 'http://(?:www\.)russia\.ru/player/main\.swf\?(\d+)',
            'params' => ['id' => 1],
            'data' => '<object width="640" height="360"><embed src="http://www.russia.ru/player/main.swf?{id}" flashvars="from=blog&blog=true" width="640" height="360" allowScriptAccess="always" allowFullScreen="true"/></object>',
        ],
        [
            'pattern' => 'https?://vk\.com/video_ext\.php\?oid=(\d+)&id=(\d+)&hash=([\d\w\-_]+)',
            'params' => ['oid' => 1, 'id' => 2, 'hash' => 3],
            'data' => '<iframe src="https://vk.com/video_ext.php?oid={oid}&id={id}&hash={hash}&hd=1" width="640" height="360" frameborder="0"></iframe>',
        ],
        [
            'pattern' => 'http://mreporter\.ru/videos/(\d+)',
            'params' => ['id' => 1],
            'data' => '<iframe width="640" height="360" src="http://mreporter.ru/videos/{id}" frameborder="0" allowfullscreen="true"></iframe>',
        ],
        [
            'pattern' => 'http://(?:www\.)?vimeo\.com/(\d+)',
            'params' => ['id' => 1],
            'data' => '<iframe src="http://player.vimeo.com/video/{id}" width="640" height="360" frameborder="0" allowFullScreen="true"></iframe>',
        ],
        [
            'pattern' => '//player\.vimeo\.com/video/(\d+)',
            'params' => ['id' => 1],
            'data' => '<iframe src="http://player.vimeo.com/video/{id}" width="640" height="360" frameborder="0" allowFullScreen="true"></iframe>',
        ],
    ];

    public static function Redactor($text)
    {
        $mediaPattern = self::$mediaPattern;

        $tags = [
            '*' => function (DomDocument $document, DOMElement $element) {
                $tagName = strtolower($element->tagName);

                switch ($tagName) {
                        case 'img':
                            $allowStyles = ['width', 'height'];
                            break;
                        default:
                            $allowStyles = [];
                    }

                if (sizeof($allowStyles) === 0) {
                    $element->removeAttribute('style');
                } else {
                    $styles = [];

                    foreach (explode(';', $element->getAttribute('style')) as $style) {
                        if (($style = trim($style)) == '') {
                            continue;
                        }

                        list($name, $property) = explode(':', $style);

                        $name = trim($name);
                        $property = trim($property);

                        if (false === in_array($name, $allowStyles)) {
                            continue;
                        }

                        if ($name && strlen($property)) {
                            $styles[$name] = $name . ':' . $property;
                        }
                    }

                    if (sizeof($styles)) {
                        $element->setAttribute('style', implode(';', $styles) . ';');
                    } else {
                        $element->removeAttribute('style');
                    }
                }
            },
            'media' => [
                'tags' => ['iframe', 'object'],
                'func' => function (DomDocument $document, DOMElement $element) use ($mediaPattern) {
                    switch (strtolower($element->tagName)) {
                            case 'iframe':
                                $content = $element->getAttribute('src');
                                break;

                            case 'object':
                                $content = $element->getElementsByTagName('embed')->item(0)->getAttribute('src');
                                break;

                            default:
                                return;
                        }

                    $isValid = false;

                    foreach ($mediaPattern as $media) {
                        if (preg_match('@' . $media['pattern'] . '@', $content, $matches)) {
                            $isValid = true;

                            $result = $media['data'];

                            foreach ($media['params'] as $name => $index) {
                                $result = str_replace('{' . $name . '}', $matches[$index], $result);
                            }
                            $element->parentNode->innerHTML = $result;
                            break;
                        }
                    }

                    if ($isValid) {
                        foreach ($element->childNodes as $child) {
                            $element->removeChild($child);
                        }
                    } else {
                        $element->parentNode->removeChild($element);
                    }
                },
            ],
            'code' => [],
            'noindex' => [],
            'pre' => [],
            'span' => [],
            'font' => [],
            'div' => [],
            'label' => [],
            'a' => function (DomDocument $document, DOMElement $element) {
                $attrs = ['title', 'alt', 'href', 'target'];

                foreach ($element->attributes as $attr) {
                    $attr = strtolower($attr->name);

                    if (false === in_array($attr, $attrs)) {
                        $element->removeAttribute($attr);
                    }
                }

                if (\App::$Env->site->name . DOMAIN_SUFFIX != parse_url($element->getAttribute('href'), PHP_URL_HOST)) {
                    $element->setAttribute('target', '_blank');
                } else {
                    $element->removeAttribute('target');
                }
            },
            'br' => [],
            'p' => [],
            'b' => [],
            'i' => [],
            'u' => [],
            'del' => [],
            'strike' => [],
            'img' => function (DomDocument $document, DOMElement $element) {
                $attrs = ['src', 'title', 'alt', 'style', 'width', 'height'];

                foreach ($element->attributes as $attr) {
                    $attr = strtolower($attr->name);

                    if (false === in_array($attr, $attrs)) {
                        $element->removeAttribute($attr);
                    }
                }

                foreach ($attrs as $attr) {
                    switch ($attr) {
                            case 'width':
                            case 'height':
                                $element->removeAttribute($attr);

                                break;

                            default:
                                if (trim($element->getAttribute($attr)) == '') {
                                    $element->removeAttribute($attr);
                                }

                                break;
                        }
                }

                $allowStyles = ['width', 'height'];

                $styles = [];

                foreach (explode(';', $element->getAttribute('style')) as $style) {
                    list($name, $property) = explode(':', $style);

                    if ($name && strlen($property)) {
                        $styles[$name] = $property;
                    }
                }

                if (true === isset($styles['width']) && $styles['width'] <= 0) {
                    unset($styles['width']);
                }

                if (true === isset($styles['height']) && $styles['height'] <= 0) {
                    unset($styles['height']);
                }

                $width = 500;
                $height = 500;

                if (isset($styles['width']) && isset($styles['height'])) {
                    if ($styles['width'] > $width || $styles['height'] > $height) {
                        $sr = (int) $styles['width'] / (int) $styles['height'];

                        if (1 < $sr) {
                            $height = round((int) $width / $sr);
                        } else {
                            $width = round((int) $height * $sr);
                        }

                        $styles['width'] = $width . 'px';
                        $styles['height'] = $height . 'px';
                    }
                } elseif (true === isset($styles['width']) && $styles['width'] > $width) {
                    $styles['width'] = $width . 'px';
                    unset($styles['height']);
                } elseif (true === isset($styles['height']) && $styles['height'] > $width) {
                    $styles['height'] = $height . 'px';
                    unset($styles['width']);
                } else {
                    $styles['max-width'] = $width . 'px';
                    $styles['max-height'] = $height . 'px';
                }

                foreach ($styles as $k => $v) {
                    if ($styles[$k]) {
                        $styles[$k] = $k . ':' . $styles[$k];
                    }
                }

                $element->setAttribute('style', implode(';', $styles) . ';');
            },
            'object' => function (DomDocument $document, DOMElement $element) {
                $el = $document->createElement('param');

                $el->setAttribute('name', 'wmode');
                $el->setAttribute('value', 'opaque');

                $element->appendChild($el);

                $attrs = ['width', 'height', 'type', 'data'];

                foreach ($element->attributes as $attr) {
                    $attr = strtolower($attr->name);

                    switch ($attr) {
                            case 'width':
                            case 'height':

                                $value = $element->getAttribute($attr);

                                if (preg_match('@^\d+%?$@', $value) && $value > 0) {
                                    continue;
                                }

                                $element->removeAttribute($attr);
                                break;

                            default:
                                if (false === in_array($attr, $attrs)) {
                                    $element->removeAttribute($attr);
                                }
                                break;
                        }
                }

                foreach ($element->childNodes as $child) {
                    if ($child->nodeName == 'embed' || $child->nodeName == 'param') {
                        continue;
                    }

                    $element->removeChild($child);
                }
            },
            'embed' => function (DomDocument $document, DOMElement $element) {
                $element->setAttribute('wmode', 'opaque');

                $attrs = ['wmode', 'width', 'height', 'src', 'type', 'quality', 'flashvars', 'allowscriptaccess', 'allowfullscreen'];

                foreach ($attrs as $attr) {
                    $value = $element->getAttribute($attr);

                    switch ($attr) {
                            case 'width':
                            case 'height':
                                if (preg_match('@^\d+%?$@', $value) && $value > 0) {
                                    continue 2;
                                }
                                break;
                            case 'wmode':
                            case 'src':
                            case 'type':
                            case 'flashvars':
                            case 'allowscriptaccess':
                            case 'allowfullscreen':
                            case 'quality':
                                if (trim($element->getAttribute($attr)) != '') {
                                    continue 2;
                                }
                                break;
                        }

                    $element->removeAttribute($attr);
                }

                foreach ($element->attributes as $attr) {
                    if (false === in_array($attr->name, $attrs)) {
                        $element->removeAttribute($attr->name);
                    }
                }

                foreach ($element->childNodes as $child) {
                    $element->removeChild($child);
                }
            },
            'param' => function (DomDocument $document, DOMElement $element) {
                if (trim($element->getAttribute('name')) == '' || trim($element->getAttribute('value')) == '') {
                    $element->parentNode->removeChild($element);
                } else {
                    foreach ($element->childNodes as $child) {
                        $element->removeChild($child);
                    }
                }
            },
            'blockquote' => function (DomDocument $document, DOMElement $element) {
                foreach ($element->childNodes as $child) {
                    $element->removeChild($child);
                }
            },
            'iframe' => function (DomDocument $document, DOMElement $element) {
                foreach ($element->childNodes as $child) {
                    $element->removeChild($child);
                }
            },
            'small' => [],
            'ul' => [],
            'ol' => [],
            'li' => [],
            'hr' => [],
            'strong' => [],
            'em' => [],
            'h1' => [],
            'h2' => [],
            'h3' => [],
            'h4' => [],
            'h5' => [],
            'h6' => [],
        ];

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->recover = true;
        $document->strictErrorChecking = false;
        $document->substituteEntities = false;

        $text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml"><html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<body>' . $text . '</body></html>';

        // Отключен вывод ошибок т.к. от пользователя частенько приходит не валидный html с recover = true он восстанавливается, но шумит в логи
        @$document->loadHTML($text);

        $xpath = new DOMXpath($document);

        $elements = $xpath->query('//body//*');

        foreach ($elements as $element) {
            /**
             * @var DOMElement
             */
            if ($element->hasAttributes() == true) {
                foreach ($element->attributes as $attr) {
                    /**
                     * @var DOMAttr
                     */
                    if (stripos($attr->name, 'on') === 0) {
                        $element->removeAttribute($attr->name);
                    }
                }
            }

            if (false === isset($tags[strtolower($element->tagName)])) {
                $element->parentNode->removeChild($element);
            } else {
                call_user_func($tags['*'], $document, $element);

                $tagName = strtolower($element->tagName);

                if (in_array($tagName, $tags['media']['tags'])) {
                    call_user_func($tags['media']['func'], $document, $element);
                }

                if (true === is_array($tags[$tagName])) {
                    continue;
                } else {
                    call_user_func($tags[$tagName], $document, $element);
                }
            }
        }

        $text = '';

        foreach ($document->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $text .= $document->saveHTML($node);
        }

        return $text;
    }

    public static function parse($element)
    {
    }

    public static function ChangeQuotes($text)
    {
        if (is_string($text)) {
            $text = str_replace("'", '&#039;', $text);
            $text = str_replace('"', '&quot;', $text);
        } elseif (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::ChangeQuotes($text[$key]);
            }
        }

        return $text;
    }

    public static function ChangeTags($text)
    {
        if (is_string($text)) {
            $text = str_replace('<', '&lt;', $text);
            $text = str_replace('>', '&gt;', $text);
        } elseif (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::ChangeTags($text[$key]);
            }
        }

        return $text;
    }

    public static function Br2NL($text)
    {
        $breaks = ['<br />', '<br>', '<br/>'];
        $text = str_ireplace($breaks, ' ', $text);

        return $text;
    }

    public static function ChangeBR($text)
    {
        if (is_string($text)) {
            $text = str_replace("\n", '<br>', $text);
        } elseif (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::ChangeBR($text[$key]);
            }
        }

        return $text;
    }

    public static function HTMLOut($text)
    {
        if (is_string($text)) {
            $text = self::ChangeBR(self::ChangeTags(self::ChangeQuotes($text)));
        } elseif (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::HTMLOut($text[$key]);
            }
        }

        return $text;
    }

    public static function HTMLOutTArea($text)
    {
        if (is_string($text)) {
            $text = self::ChangeTags(self::ChangeQuotes($text));
        } elseif (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::HTMLOutTArea($text[$key]);
            }
        }

        return $text;
    }

    public static function InputClean($input)
    {
        $input = htmlentities($input, ENT_QUOTES, 'utf8');

        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input = strip_tags($input);
        $input = addcslashes($input, "00\n\r\\32");

        return $input;
    }

    /**
     * @static
     * Формирует массив для создания ссылок стриничной навигации
     *
     * @param int $col            Количество записей на странице
     * @param int $colinblock    Количество ссылок в блоке (чтобы все страничы не вываливать)
     * @param int $c_count        Количество записей всего
     * @param int $c_p            Номер текущей страницы
     * @param string $c_link        заготовка ссылки для навигации, например: /somepart/somefile.php?a1=1&a2=2&...&p=@p@
     * @param int $c_type 1 = 1 2 3 4, 2 = 1-10 11-20 21-30
     * @param int $onepage
     *
     * @return array
     * back string      ссылка на пред. страницу
     * next string      ссылка на след. страницу
     * btn array(
     *     array(
     *         text string      текст ссылки
     *         link string      сама ссылка
     *         active int       (1 = текущая, 0 = другая)
     *     )
     * )
     */
    public static function GetNavigationPagesNumber($col, $colinblock, $c_count, $c_p, $c_link, $c_type = 1, $onepage = 0)
    {
        $list['back'] = '';
        $list['next'] = '';
        $list['btn'] = [];
        $list['current'] = str_replace('@p@', $c_p, $c_link);
        $colpage = 0;

        if ($col > 0 && (($c_count > $col) || $onepage)) {
            $colpage = ceil($c_count / $col); // кол-во страниц
            if ($c_p > $colpage) {
                return;
            }
            // если такой стриницы нет
            if ($colpage > $colinblock) {
                $colinblock1 = floor($colinblock / 2);
                $colinblock2 = $colinblock - $colinblock1 - 1;

                if (($c_p - $colinblock1) < 1) {
                    $b1 = 1;
                    $b2 = $colinblock;
                } elseif (($c_p + $colinblock2) > $colpage) {
                    $b1 = $colpage - $colinblock + 1;
                    $b2 = $colpage;
                } else {
                    $b1 = $c_p - $colinblock1;
                    $b2 = $c_p + $colinblock2;
                }
            } else {
                $b1 = 1;
                $b2 = $colpage;
            }

            if ($c_p != 1) {
                $list['back'] = str_replace('@p@', ($c_p - 1), $c_link);
                $list['first'] = str_replace('@p@', 1, $c_link);
            }

            if ($c_p != $colpage) {
                $list['next'] = str_replace('@p@', ($c_p + 1), $c_link);
                $list['last'] = str_replace('@p@', $colpage, $c_link);
            }

            for ($i = $b1; $i <= $b2; $i++) {
                $pl_start = ($col * ($i - 1)) + 1;
                $pl_end = (($col * $i) > $c_count ? $c_count : ($col * $i));

                if ($c_type == 2) {
                    $list['btn'][$i]['text'] = $pl_start . '-' . $pl_end;
                } else {
                    $list['btn'][$i]['text'] = $i;
                }
                $list['btn'][$i]['link'] = str_replace('@p@', $i, $c_link);

                if ($i == $c_p) {
                    $list['btn'][$i]['active'] = 1;
                } else {
                    $list['btn'][$i]['active'] = 0;
                }
            }
        }
        $list['c_p'] = $c_p;
        $list['colpage'] = $colpage;

        return $list;
    }
}
