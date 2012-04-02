<?php
if (class_exists('Extension_AppPreBodyRenderer',true)):
	class Cerb5blogAutorefreshNotificationsPreBodyRenderer extends Extension_AppPreBodyRenderer {
		function render() { 
                    $tpl = DevblocksPlatform::getTemplateService();
                    $tpl->display('devblocks:cerb5blog.autorefresh.notifications::header.tpl');
		}
	};
endif;

if (class_exists('DevblocksControllerExtension',true)):
class Controller_Cerb5blogAutorefreshNotificationsAjax extends DevblocksControllerExtension {
	function isVisible() {
		// The current session must be a logged-in worker to use this page.
		if(null == ($worker = CerberusApplication::getActiveWorker()))
			return false;
		return true;
	}

	/*
	 * Request Overload
	 */
	function handleRequest(DevblocksHttpRequest $request) {
		$stack = $request->path;
		array_shift($stack); // example
		
	    @$action = array_shift($stack) . 'Action';

	    switch($action) {
	        case NULL:
	            // [TODO] Index/page render
	            break;
	            
	        default:
			    // Default action, call arg as a method suffixed with Action
				if(method_exists($this,$action)) {
					call_user_func(array(&$this, $action));
				}
	            break;
	    }
	    
	    exit;
	}

	function writeResponse(DevblocksHttpResponse $response) {
		return;
	}
	
	function getUnreadNotificationsAction() {
            $active_worker = CerberusApplication::getActiveWorker();
            if(!empty($active_worker)) {
                        $tpl = DevblocksPlatform::getTemplateService();
			$unread_notifications = DAO_Notification::getUnreadCountByWorker($active_worker->id);
			$tpl->assign('active_worker_notify_count', $unread_notifications);
			$tpl->display('devblocks:cerb5blog.autorefresh.notifications::badge_notifications_script.tpl');
                    }
	}
};
endif;