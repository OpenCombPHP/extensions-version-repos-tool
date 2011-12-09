<?php
namespace org\opencomb\development\repos ;

use org\jecat\framework\fs\imp\LocalFolder;
use org\jecat\framework\fs\FileSystem;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ReposAction extends ControlPanel
{
	public function actionPull() 
	{
		if( empty($this->params['path']) )
		{
			$this->createMessage(Message::error,"缺少 path 参数") ;
			return ;
		}
		
		if( !$aReposFolder = FileSystem::singleton()->findFolder($this->params['path']) )
		{
			$this->createMessage(Message::error,"path : %s 不是一个存在的目录") ;
			return ;
		}
		
		if( !$aReposFolder->findFolder('.git') or !($aReposFolder instanceof LocalFolder) )
		{
			$this->createMessage(Message::error,"path : %s 不是一个git版本库") ;
			return ;
		}
		
		self::chdir($aReposFolder) ;
		
		
		echo $sOutput = 'gits pull origin master',`gits pull origin master` ;
		
		$this->createMessage(Message::notice,$sOutput) ;
	}
	
	static public function chdir(LocalFolder $aFolder)
	{
		echo $sPath = preg_replace('|^file://|','',$aFolder->url()) ;
		chdir($sPath) ;
	}
}

?>