<div class="page-menu" id="page_menu">
        <div class="menu-block">
                <div class="title-block">
                        <div class="page-title"><? echo $la['MENU']; ?></div>
                        <a href="#" onclick="hideMenuPage();">
                                <i class="glyphicon glyphicon-remove"></i>
                        </a>
                </div>
                
                <ul class="menu clearfix"> 
                        <li>
                                <a href="#" id="page_menu_map" onclick="switchPage('map');">
                                        <i class="glyphicon glyphicon-globe"></i>
                                        <? echo $la['MAP']; ?>	
                                </a>
                        </li>  
                        <li>
                                <a href="#" id="page_menu_objects" onclick="switchPage('objects');">
                                        <i class="glyphicon glyphicon-map-marker"></i>
                                        <? echo $la['OBJECTS']; ?>
                                </a>
                        </li>
                        <li>
                                <a href="#" id="page_menu_events" onclick="switchPage('events');">
                                        <i class="glyphicon glyphicon-alert"></i>
                                        <? echo $la['EVENTS']; ?>
                                </a>
                        </li>  
                        <li>
                                <a href="#" id="page_menu_history" onclick="switchPage('history');">
                                        <i class="glyphicon glyphicon-history"></i>
                                        <? echo $la['HISTORY']; ?>
                                </a>
                        </li>
                        <li>
                                <a href="#" id="page_menu_object_control" onclick="switchPage('object_control');">
                                        <i class="glyphicon glyphicon-send"></i>
                                        <? echo $la['OBJECT_CONTROL']; ?>
                                </a>
                        </li> 
                        <li>
                                <a href="#" id="page_menu_settings" onclick="switchPage('settings');">
                                        <i class="glyphicon glyphicon-cog"></i>
                                        <? echo $la['SETTINGS']; ?>
                                </a>
                        </li> 
                </ul>
                
                <hr/>
                
                <div class="buttons-block">
                                <a style="margin-right: 15px;" class="desktop-btn" href="../tracking.php" data-ajax="false">
                                        <i class="glyphicon glyphicon-desktop"></i>
                                        <? echo $la['DESKTOP_VERSION']; ?>
                                </a>
                                
                                <a class="logout-btn" href="#" onClick="connectLogout();">
                                        <i class="glyphicon glyphicon-exit"></i>
                                        <? echo $la['LOGOUT']; ?>
                                </a>
                </div>
        </div>
</div>