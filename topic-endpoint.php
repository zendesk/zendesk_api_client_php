<?php
include("vendor/autoload.php");
 
use Zendesk\API\Client as ZendeskAPI;
 
$subdomain = "";
$username = "";
$token = ""; // replace this with your token
$user = ""; //set a user to creat a test topic from
$forum =""; //set a forum to put a test topic in
 
$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password
 
//Create a new topic, and a persistent one from the submit box
$newtopic = $client->topics()->create(array(
'title'=>'The Title',
'body'=>'The Body',
'submitter_id'=>$user,
'forum_id'=>$forum
))->topic;
 
//create a new topic if the form is submitted
if (!empty($_POST)) {
$client->topics()->create(array(
'title'=>$_POST['title'],
'body'=>$_POST['body'],
'submitter_id'=>$_POST['submitter'],
'forum_id'=>$_POST['forum']
));
}
 
//get all topics
$topics = $client->topics()->findAll()->topics;
 
//print the topics
foreach($topics as $topic){
echo "<div id='$topic->id' class='catinfo'>
<p>Title: <a href='$topic->url'>$topic->title</a></p>
<p>Body: $topic->body</p>
</div>";
}
 
//edit a topic
$client->topics($newtopic->id)->update(array(
'pinned'=>true
));
 
//delete the topic
$client->topics($newtopic->id)->delete();
 
?>
 
<form action="topic-endpoint.php" method="post">
Title: <input type="text" name="title"><br/>
Body: Title: <input type="text" name="body"><br/>
 
Submitter:
<select name="submitter">
<?php
foreach($client->users()->findAll()->users as $user){
echo "<option value='$user->id''>$user->name</option>";
}
?>
</select>
<br/>
Forum:
<select name="forum">
<?php
foreach($client->forums()->findAll()->forums as $forum){
echo "<option value='$forum->id''>$forum->name</option>";
}
?>
</select>
<br/>
<input type="submit"/>
</form>