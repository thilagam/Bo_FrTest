<?

// require zend loader class
require_once ROOT_PATH.'Zend/Loader.php';



//zend resources
Zend_Loader::loadClass('Zend_View');
Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Session_Namespace');
Zend_Loader::loadClass('Zend_Currency');
Zend_Loader::loadClass('Zend_Http_Client');
Zend_Loader::loadClass('Zend_Cache');

//shiva : database package
Zend_Loader::loadClass('Ep_Db_Xml');
Zend_Loader::loadClass('Ep_Db_XmlDb');
Zend_Loader::loadClass('Ep_Db_NewXmlDb');
Zend_Loader::loadClass('Ep_Db_ArrayDb');
Zend_Loader::loadClass('Ep_Db_ArrayDb2');
Zend_Loader::loadClass('Ep_Db_Category');

//milan : database package
Zend_Loader::loadClass('Ep_Db_DbController');
Zend_Loader::loadClass('Ep_Db_DbTable');
Zend_Loader::loadClass('Ep_Db_Identifier');

Zend_Loader::loadClass('Zend_Mail');
Zend_Loader::loadClass('Zend_Paginator');
Zend_Loader::loadClass('Zend_View_Helper_PaginationControl');
Zend_Loader::loadClass('Zend_Controller_Front');

//farid : controller package
Zend_Loader::loadClass('Ep_Controller_Action');
Zend_Loader::loadClass('Ep_Controller_View');
Zend_Loader::loadClass('Ep_Controller_Page');
Zend_Loader::loadClass('Ep_Controller_Module');
Zend_Loader::loadClass('Ep_Controller_Pattern');
Zend_Loader::loadClass('Ep_Controller_Balance');


Zend_Loader::loadClass('Ep_User_User');
Zend_Loader::loadClass('Ep_User_Configuration');
Zend_Loader::loadClass('Ep_User_RecentActivities');
Zend_Loader::loadClass('Ep_User_Client');
Zend_Loader::loadClass('Ep_User_UserPlus');
Zend_Loader::loadClass('Ep_User_DailyNewsletter');
Zend_Loader::loadClass('Ep_User_Newsletter');
Zend_Loader::loadClass('Ep_User_WhitebookDownloads');
Zend_Loader::loadClass('Ep_User_UserLogins');
Zend_Loader::loadClass('Ep_User_UserLogs');//added by naseer on 09-11-2015//

Zend_Loader::loadClass('Ep_Ao_Delivery');
Zend_Loader::loadClass('Ep_Ao_Article');
Zend_Loader::loadClass('Ep_Ao_Payment');
Zend_Loader::loadClass('Ep_Ao_Participation');
Zend_Loader::loadClass('Ep_Ao_DeliveryComment');
Zend_Loader::loadClass('Ep_Ao_PaymentArticle');
Zend_Loader::loadClass('Ep_Ao_AdComments');
Zend_Loader::loadClass('Ep_Ao_Favouritecontributor');
Zend_Loader::loadClass('Ep_Ao_Data');
Zend_Loader::loadClass('Ep_Ao_PremiumQuotes');
Zend_Loader::loadClass('Ep_Ao_QuotesHistory');

//Arun: Contributor classes
Zend_Loader::loadClass('Ep_Contrib_Registration');
Zend_Loader::loadClass('Ep_Contrib_ProfilePlus');
Zend_Loader::loadClass('Ep_Contrib_ProfileContributor');
Zend_Loader::loadClass('Ep_Contrib_Experience');
Zend_Loader::loadClass('Ep_Contrib_Image');
Zend_Loader::loadClass('Ep_Contrib_Contact');
Zend_Loader::loadClass('Ep_Contrib_WhiteListKeywords');
Zend_Loader::loadClass('Ep_Contrib_HotelsKeywords');

//Arun: Artilce and Delivery Related
Zend_Loader::loadClass('Ep_Article_Article');////////////////////////////
Zend_Loader::loadClass('Ep_Article_Delivery');
Zend_Loader::loadClass('Ep_Article_ArticleProcess');
Zend_Loader::loadClass('Ep_Article_DeliveryOptions');
Zend_Loader::loadClass('Ep_Article_Options');
Zend_Loader::loadClass('Ep_Article_Pricenbwords');
Zend_Loader::loadClass('Ep_Article_XmlDecoder');
Zend_Loader::loadClass('Ep_Article_DeliveryAlert');
Zend_Loader::loadClass('Ep_Article_ArticleActions');
Zend_Loader::loadClass('Ep_Article_ArticleHistory');
Zend_Loader::loadClass('Ep_Article_ArticleReassignReasons');
Zend_Loader::loadClass('Ep_Article_Ebooker');
//Arun :Mail classes
Zend_Loader::loadClass('Ep_Ticket_Message');
Zend_Loader::loadClass('Ep_Ticket_Ticket');
Zend_Loader::loadClass('Ep_Ticket_Attachment');
Zend_Loader::loadClass('Ep_Ticket_AutoEmails');

//Arun :Royalties & Invoices
Zend_Loader::loadClass('Ep_Royalty_Royalties');
Zend_Loader::loadClass('Ep_Royalty_Invoice');

//Arun: Participation Related
Zend_Loader::loadClass('Ep_Participation_Participation');
Zend_Loader::loadClass('Ep_Participation_Watchlist');

//Arun //Corrector Participation
Zend_Loader::loadClass('Ep_Participation_CorrectorParticipation');

//Arun Stats class for all footer stats
Zend_Loader::loadClass('Ep_Stats_Stats');

//Arun Antiword Class
Zend_Loader::loadClass('Ep_Antiword_Antiword');

//Poll
Zend_Loader::loadClass('Ep_Poll_Poll');
Zend_Loader::loadClass('Ep_Poll_Participation');
Zend_Loader::loadClass('Ep_Poll_UserResponse');

//Arun Ads Comments
Zend_Loader::loadClass('Ep_Comments_Adcomments');

//Arun  Recruitment Participation
Zend_Loader::loadClass('Ep_Recruitment_Participation');

//Arun Quiz Class
Zend_Loader::loadClass('Ep_Quiz_Quiz');
Zend_Loader::loadClass('Ep_Quiz_Participation');
Zend_Loader::loadClass('Ep_Quiz_UserResponse');
Zend_Loader::loadClass('Ep_Stats_NewsletterStats');

/* ***n added on 25.02.2016 *** */
Zend_Loader::loadClass('Ep_Bnp_Bnp');
//Naseer loadClass for corn job
//Zend_Loader::loadClass('Ep_Payment_Invoice');

