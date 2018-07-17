<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPHtmlParser\Dom;
use Sunra\PhpSimple\HtmlDomParser;

class Main extends CI_Controller {

    public function index()
    {
        $dom  = new Dom;
        $url  = $dom->loadFromUrl('http://blog.mitschool.co.id/belajar-ionic-framework/');
        $html = $url->outerHtml;
        $html = HtmlDomParser::str_get_html($html);
        $content = $html->find('div.single-blog-content', 0)->innertext;
        $publishDate = $html->find('meta[property=article:published_time]', 0)->content;

        foreach ($html->find('a[rel=category tag]') as $val) :
            $category[] = strtolower($val->plaintext);
        endforeach;

        $data = [
            'publish_at' => date("Y-m-d", strtotime($publishDate)),
            'title'      => $html->find('h1', 0)->plaintext,
            'img'        => $html->find('img.size-full', 0)->src,
            'content'    => $content,
            'author'	 => $html->find('div.blog-meta a[title]', 0)->plaintext,
            'category'	 => $category,
        ];

        $fp = fopen('assets/blog.json', 'w');
        fwrite($fp, json_encode($data));
        fclose($fp); 
    }

    public function asiangames()
    {
        $dom  = new Dom;
        $url  = $dom->loadFromUrl('https://id.wikipedia.org/wiki/Tabel_perolehan_medali_Olimpiade_Musim_Panas_2016');
        $html = $url->outerHtml;
        $html = HtmlDomParser::str_get_html($html);

        foreach ($html->find('table[class=plainrowheaders] tbody tr') as $val) :
            $dom->load($val);
            $a = $dom->find('th a')[0];
            if(isset($a) && $a->text != '') :
                $data['negara'][] = $a->text;
            endif;
            $span = $dom->find('th[scope=row] span')[0];
            if(isset($span) && $span->text != '') :
                $data['kode'][] = $span->text;
            endif;
            $emas = $dom->find('td')[1];
            if(isset($emas) && $emas->text != '' && is_numeric($emas->text)) :
                $data['emas'][] = $emas->text;
            endif;
            $perak = $dom->find('td')[2];
            if(isset($perak) && $perak->text != '' && is_numeric($perak->text)) :
                $data['perak'][] = $perak->text;
            endif;
            $perunggu = $dom->find('td')[3];
            if(isset($perunggu) && $perunggu->text != '' && is_numeric($perunggu->text)) :
                $data['perunggu'][] = $perunggu->text;
            endif;
            $jumlah = $dom->find('td')[3];
            if(isset($jumlah) && $jumlah->text != '' && is_numeric($jumlah->text)) :
                $data['jumlah'][] = $jumlah->text;
            endif;
        endforeach;

        foreach ($data['negara'] as $key => $value) {
            $json[] = [
                'kode'     => $data['kode'][$key],
                'negara'   => $value,
                'emas'     => $data['emas'][$key],
                'perak'    => $data['perak'][$key],
                'perunggu' => $data['perunggu'][$key],
                'jumlah'   => $data['jumlah'][$key],
            ];
        }

        $fp = fopen('assets/asiangames.json', 'w');
        fwrite($fp, json_encode($json));
        fclose($fp); 
    }
}
