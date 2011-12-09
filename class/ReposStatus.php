<?php
namespace org\opencomb\development\repos ;

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
		$arrAllReposStatus = array() ;
		
		$arrAllReposStatus['framework'] = array(
				'type' => '框架' ,
				'path' => '/framework' ,
		) ;
		$this->gitStatus(FileSystem::singleton()->findFolder('/framework'),$arrAllReposStatus['framework']) ;
			
		$arrAllReposStatus['platform'] = array(
				'type' => '平台' ,
				'path' => '/' ,
		) ;
		$this->gitStatus(FileSystem::singleton()->findFolder('/'),$arrAllReposStatus['platform']) ;
		
		$aExtMgr = ExtensionManager::singleton() ;
		foreach($aExtMgr->metainfoIterator() as $aExtMeta)
		{
			$aExtMeta instanceof ExtensionMetainfo ;
			
			$sPath = $aExtMeta->installPath() ;
			
			$sExtName = $aExtMeta->name() ;
			$arrAllReposStatus[$sExtName]['type'] = '扩展' ;
			$arrAllReposStatus[$sExtName]['path'] = $sPath ;
			
			$aInstallFolder = FileSystem::singleton()->findFolder($sPath) ;
			$this->gitStatus($aInstallFolder,$arrAllReposStatus[$sExtName]) ;
		}
		$this->statusView->variables()->set('arrAllReposStatus',$arrAllReposStatus) ;
	}
	
	private function gitStatus(LocalFolder $aFolder,&$arrStatus)
	{
		if( FileSystem::singleton()->findFolder($aFolder->path().'/.git') )
		{
			$arrStatus['repos'] = 'git' ;
			
			if($aFolder instanceof LocalFolder)
			{
				ReposAction::chdir($aFolder) ;
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