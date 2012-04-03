<?php
if(class_exists('Extension_PluginSetup')):
    class Cerb5blogAutorefreshNotifications_Setup extends Extension_PluginSetup {
        const POINT = 'cerb5blog.autorefresh.notifications.setup';

        function render() {
            $tpl = DevblocksPlatform::getTemplateService();

            $params = array(
                'refreshrate' => DevblocksPlatform::getPluginSetting('cerb5blog.autorefresh.notifications','refreshrate','60'),
            );
            $tpl->assign('params', $params);
            
            $tpl->display('devblocks:cerb5blog.autorefresh.notifications::setup.tpl');
        }

        function save(&$errors) {
            try {
                @$refreshrate = DevblocksPlatform::importGPC($_REQUEST['refreshrate'],'string','60');

                if(empty($refreshrate))
                    throw new Exception("Must set a refresh rate.");
                
                if(($refreshrate < 5) || ($refreshrate > 1000))
                    throw new Exception("Range must be between 5 and 1000 seconds.");
                
                DevblocksPlatform::setPluginSetting('cerb5blog.autorefresh.notifications','refreshrate',$refreshrate);

                return true;

            } catch (Exception $e) {
                $errors[] = $e->getMessage();
                return false;
            }
        }
    };
endif;

if (class_exists('Extension_AppPreBodyRenderer',true)):
    class Cerb5blogAutorefreshNotificationsPreBodyRenderer extends Extension_AppPreBodyRenderer {
        function render() { 
            $tpl = DevblocksPlatform::getTemplateService();
            $refreshrate = intval(DevblocksPlatform::getPluginSetting('cerb5blog.autorefresh.notifications','refreshrate','60'));
            if(($refreshrate < 5) || ($refreshrate > 1000)) {
                $refreshrate = 60;
            }
            $tpl->assign('refreshrate', $refreshrate);
            $tpl->display('devblocks:cerb5blog.autorefresh.notifications::header.tpl');
        }
    };
endif;

if (class_exists('DevblocksControllerExtension',true)):
class Controller_Cerb5blogAutorefreshNotificationsAjax extends DevblocksControllerExtension {
	function isVisible() {
		// The current session must be a logged-in worker to use this page.
		//if(null == ($worker = CerberusApplication::getActiveWorker()))
		//	return false;
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
            $tpl = DevblocksPlatform::getTemplateService();
            $active_worker = CerberusApplication::getActiveWorker();
            if(!empty($active_worker)) {
		$unread_notifications = DAO_Notification::getUnreadCountByWorker($active_worker->id);
		$tpl->assign('active_worker_notify_count', $unread_notifications);
                $tpl->display('devblocks:cerb5blog.autorefresh.notifications::badge_notifications_script.tpl');
            } else {
                $tpl->display('devblocks:cerb5blog.autorefresh.notifications::session_expire.tpl');
            }
	}
};
endif;