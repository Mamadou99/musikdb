<?php
/**
 * SFileTree is a wrapper for the jQuery File Tree plugin<br />
 * http://abeautifulsite.net/notebook/58 </br>
 * For the easing plugin documentation check <br />
 * http://gsgd.co.uk/sandbox/jquery/easing/ </br>
 */
class SFileTree extends CWidget {

  private $_baseUrl = "";
/* @var $div String : The container where the file tree will be displayed*/
  public $div;
/* @var $root String : root folder to display*/
  public $root;
  public $script;
  private $_script = "jqueryFileTree.php";
/* @var $folderEvent String : Event to trigger expand/collapse*/
  public $folderEvent = "click";
/* @var $expandSpeed int : Speed at which to expand branches (in milliseconds);
 * use -1 for no animation*/
  public $expandSpeed = "500";
/* @var $collapseSpeed int : Speed at which to expand branches (in milliseconds);
 * use -1 for no animation*/
  public $collapseSpeed = "500";
/* @var $expandEasing String : Easing function to use on expand*/
  public $expandEasing = "";
/* @var $collapseEasing String : Easing function to use on collapse*/
  public $collapseEasing = "";
/* @var $multiFolder boolean : Whether or not to limit the browser to one
 * subfolder at a time*/
  public $multiFolder = true;
/* @var $loadMessage String : Message to display while initial tree loads
 * (can be HTML)*/
  public $loadMessage = "Loading...";
/* @var $callback String : The javascript function to execute when a file is
 * selected*/
  public $callback = "alert";

  /**
   * Execute the widget
   */
  public function run() {
    $this->registerClientScripts();
    $this->createFileTree();
  }

  /**
   * Registers the clientside widget files (css & js)
   */
  public function registerClientScripts() {
  //Yii::app()->clientScript->registerCoreScript('jquery');
  // Get the resources path
    $resources = dirname(__FILE__).DIRECTORY_SEPARATOR.'resources';

    // publish the files
    $this->_baseUrl = Yii::app()->assetManager->publish($resources);

    //Debug : publish style in every request

    // register the files
    Yii::app()->clientScript->registerScriptFile($this->_baseUrl.'/jqueryFileTree.js');
  }

  /**
   * Creates and register the filetree script
   */
  public function createFileTree() {
    $script = "$('#".$this->div."').fileTree({";

    $s['root'] = $this->root ? "root:'".$this->root."'" : "root:'/'";
    $s['script'] = $this->script ? "script:'".$this->script."'" : "script:'".$this->_baseUrl."/".$this->_script."'";
    $s['folderEvent'] = $this->folderEvent ? "folderEvent:'".$this->folderEvent."'" : "folderEvent:'click'";
    $s['expandSpeed'] = $this->expandSpeed ? "expandSpeed:'".$this->expandSpeed."'" : "expandSpeed:'500'";
    $s['collapseSpeed'] = $this->collapseSpeed ? "collapseSpeed:'".$this->collapseSpeed."'" : "collapseSpeed:'500'";
    $s['expandEasing'] = $this->expandEasing ? "expandEasing:'".$this->expandEasing."'" : "";
    $s['collapseEasing'] = $this->collapseEasing ? "collapseEasing:'".$this->collapseEasing."'" : "";
    $s['multiFolder'] = $this->multiFolder ? "multiFolder:".$this->multiFolder : "multiFolder:true";
    $s['loadMessage'] = $this->loadMessage ? "loadMessage:'".$this->loadMessage."'" : "loadMessage:'Loading...'";
    $callback = $this->callback ? $this->callback : "alert";
    $s = array_filter($s);
    $script .= implode(",\n", $s);

    $script .= "},function(file) {";
    $script .= $callback."(file)";
    $script .= "});";
    
    Yii::app()->clientScript->registerScript("cb",$script,CClientScript::POS_READY);

  }
}
?>
