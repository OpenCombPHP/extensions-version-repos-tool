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
		$arrAllReposStatus['framework'] = array(
				'type' => '框架' ,
				'folder' => FileSystem::singleton()->findFolder('/framework') ,
				'title' => 'JeCat PHP Framework' ,
		) ;
		$this->gitStatus($arrAllReposStatus['framework']) ;
			
		$arrAllReposStatus['platform'] = array(
				'type' => '平台' ,
				'folder' => FileSystem::singleton()->findFolder('/') ,
				'title' => '蜂巢' ,
		) ;
		$this->gitStatus($arrAllReposStatus['platform']) ;
		
		$aExtMgr = ExtensionManager::singleton() ;
		foreach($aExtMgr->metainfoIterator() as $aExtMeta)
		{
			$aExtMeta instanceof ExtensionMetainfo ;
			
			$sExtName = $aExtMeta->name() ;
			$arrAllReposStatus[$sExtName]['type'] = '扩展' ;
			$arrAllReposStatus[$sExtName]['folder'] = FileSystem::singleton()->findFolder($aExtMeta->installPath()) ;
			$arrAllReposStatus[$sExtName]['title'] = $aExtMeta->title() ;
			
			$this->gitStatus($arrAllReposStatus[$sExtName]) ;
		}
		$this->statusView->variables()->set('arrAllReposStatus',$arrAllReposStatus) ;
	}
	
	private function gitStatus(&$arrStatus)
	{
		if( $arrStatus['folder']->findFolder('.git') )
		{
			$arrStatus['repos'] = 'git' ;
			
			if($arrStatus['folder'] instanceof LocalFolder)
			{
				chdir($arrStatus['folder']->url(false)) ;
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