<?php
namespace com\opencomb\development\repos ;

use org\jecat\framework\fs\imp\LocalFolder;
use org\jecat\framework\fs\FileSystem;
use org\opencomb\ext\ExtensionMetainfo;
use org\opencomb\ext\ExtensionManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class ReposStatus extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'view:statusView' => array(
					'template' => 'ReposStatus.html'
				) ,				
		) ;
	}
	
	public function process()
	{
		$arrExtsStatus = array() ;
		
		$arrExtsStatus['framework'] = array(
				'type' => '框架'
		) ;
		$this->gitStatus(FileSystem::singleton()->findFolder('/framework'),$arrExtsStatus['framework']) ;
			
		$arrExtsStatus['platform'] = array(
				'type' => '平台'
		) ;
		$this->gitStatus(FileSystem::singleton()->findFolder('/'),$arrExtsStatus['platform']) ;
		
		$aExtMgr = ExtensionManager::singleton() ;
		foreach($aExtMgr->metainfoIterator() as $aExtMeta)
		{
			$aExtMeta instanceof ExtensionMetainfo ;
			
			$sExtName = $aExtMeta->name() ;
			$arrExtsStatus[$sExtName]['type'] = '扩展' ;
			
			$sPath = $aExtMeta->installPath() ; 
			
			$aInstallFolder = FileSystem::singleton()->findFolder($sPath) ;
			$this->gitStatus($aInstallFolder,$arrExtsStatus[$sExtName]) ;
		}
		$this->statusView->variables()->set('arrExtsStatus',$arrExtsStatus) ;
	}
	
	private function gitStatus(LocalFolder $aFolder,&$arrStatus)
	{
		if( FileSystem::singleton()->findFolder($aFolder->path().'/.git') )
		{
			$arrStatus['repos'] = 'git' ;
			
			if($aFolder instanceof LocalFolder)
			{
				$sPath = preg_replace('|^file://|','',$aFolder->url()) ;
				chdir($sPath) ;
				$arrStatus['detail'] = `git status -s` ;
				$arrStatus['status'] = empty($arrStatus['detail'])? 'clean': 'modified' ;
			}
			else
			{
				$arrStatus['status'] = 'unknow' ;
			}
		}
		
		else
		{
			$arrStatus['repos'] = 'none' ;
		}
	}
	
}

?>