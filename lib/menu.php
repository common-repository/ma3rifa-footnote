<?php

class ma3rifaSettingPage{
  public static $key='ma3rifa_option_name';//オプションへの保存、呼び出しキー

  private $html_title='Ma3rifa Footnote Config.';//HTMLのタイトル（管理ページのtitleタグ）
  private $page_title='Ma3rifa Footnote';//ページのタイトル
  private $page_slug='ma3rifa_option_page';

  private $options;
  private $group='ma3rifa_option_group';
  private $section='ma3rifa_setting_admin';

  //ここにキーとタイトル、コールバックをセットにして
  public static function getFields(){

    return array(
      array(
        'type'=>'section',//区切りを入れるときはtypeをセクションにする
        'name'=>'section1',
        'title'=>'Enter Mediawiki endpoint',
        'callback'=>'section_callback',
      ),
      array(
        'type'=>'field',//フィールドかセクション　お好みに合わせて追加
        'name'=>'wikihost',//名前
        'title'=>'Host (http://ma3rifa.hijra.jp , https://ja.wikipedia.org , etc)',//タイトル
        'callback'=>'text_callback',//コールバック
      ),
      array(
        'type'=>'field',
        'name'=>'wikiapi',
        'title'=>'API (usually "/w/api.php")',
        'callback'=>'text_callback',
      ),
    );
  }

  //初期化
  public function __construct(){
      add_action('admin_menu',array($this,'add_my_option_page'));
      add_action('admin_init',array($this,'page_init'));
  }

  //キーを取得（外部から呼び出せるようにする）
  public static function getKey(){
    return self::$key;
  }

  //設定
  public function add_my_option_page(){
      add_options_page(
          $this->html_title,//ダッシュボードのメニューに表示するテキスト
          $this->page_title,//ページのタイトル
          'edit_themes',
          $this->page_slug,//ページスラッグ
          array( $this, 'create_admin_page' )
      );
  }

  //フォームの外観作成
  public function create_admin_page(){
      // Set class property
      $this->options = get_option($this->getKey());
      ?>
      <div class="wrap">
          <?php //screen_icon(); ?>
          <h2><?php echo $this->page_title;?></h2>
          <p>List of settings:</p>
          <form method="post" action="options.php">
          <?php
              // This prints out all hidden setting fields
              settings_fields($this->group);
              do_settings_sections($this->section);
              submit_button();
          ?>
          </form>
 <p>         The MIT License (MIT) </p> 

 <p> Copyright (c) 2013-2014 Chris Sauve </p> 

 <p> Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions: </p> 

 <p> The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software. </p> 

 <p> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE. </p> 
      </div>
      <?php
  }

  //フォームの部品組み立て
  public function page_init(){
    register_setting(
      $this->group, // Option group
      $this->getKey(), // Option name
      array( $this, 'sanitize' ) // Sanitize
    );

    $fields=$this->getFields();
    $section_id='';
    foreach($fields AS $field){
      if($field['type']=='field'){
        add_settings_section(
          $field['name'], // ID
          $field['title'], // Title
          array($this,$field['callback']), // Callback
          $this->section, // Page
          $section_id
        );
      }else{
        add_settings_section(
          $field['name'], // ID
          $field['title'], // Title
          array($this,$field['callback']), // Callback
          $this->section // Page
        );
        $section_id=$field['name'];
      }
    }
  }

  //保存前のサニタイズ
  public function sanitize($input){

    $new_input = array();
    foreach($this->getFields() AS $field){
      if(isset($input[$field['name']])){
        $new_input[$field['name']] = sanitize_text_field($input[$field['name']]);
      }
    }
    return $new_input;
  }

  //セクション表示関数
  public function section_callback(array $args){
    echo '<hr>';
  }

  //テキストフィール表示関数
  public function text_callback(array $args){
    $name=$args['id'];
    printf(
      '<input type="text" id="'.$name.'" name="'.$this->getKey().'['.$name.']" value="%s" />',
      isset( $this->options[$name] ) ? esc_attr( $this->options[$name]) : ''
    );
  }


}

if(is_admin())
    $my_settings_page = new ma3rifaSettingPage();