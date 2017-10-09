<?php
require"../engine/php/php.php";
if(!isset($_SESSION['id'])||$_SESSION['id']==""||$_SESSION['id']==0)page();
if(!isset($_GET['id']))page("profile.php?id=".$_SESSION['id']);
menu();
mO();

$id=$_GET['id'];

//ЗАЩИТА БАЗЫ ДАННЫХ
if($id=="")page("profile.php?id=".$_SESSION['id']);
$id = preg_replace("/[^0-9]/", '', $id);
//ЗАЩИТА БАЗЫ ДАННЫХ

$q=mysqli_query($cn,"select * from `users` where id = ".$id."");
$e=mysqli_num_rows($q);
if($e==0){echo"Данный пользователь не найден!";head("Пользователь не найден");}
while($w=mysqli_fetch_assoc($q)):
$nick = $w['login'];
$status = $w['tstatus'];
$dir = $w['dir'];
if($status==""&&$id==$_SESSION['id']){
$status="Нажмите чтобы изменить статус";
}
head($nick);
if($w['delete_user']!=0&&$w['ban_user']==0){
echo '
<div class="mr20 centered"><h4>Данный пользователь удалён, либо ещё не создан!</h4></div>';
exit();
}
if($w['delete_user']==0&&$w['ban_user']!=0){
echo '
<div class="mr20 centered"><h4>Данный пользователь забанен за нарушение правил!</h4></div>';
exit();
}
if($w['delete_user']!=0&&$w['ban_user']!=0){
echo '
<div class="mr20 centered"><h4>Данный пользователь забанен и удалён с сайта!</h4></div>';
exit();
}

if(isset($_POST['status'])){
$text = $_POST['text'];
if($text!=""){
mysqli_query($cn,"update `users` set tstatus = '".$text."' where id = ".$_SESSION['id']."");
page(1);
}else{
mS(1,"Статус не может быть пустым!");
}
}

if(isset($_POST['wall'])){
$text = $_POST['text'];
$imagew = $_FILES['userfile'];
var_dump($_FILES);

if(isset($text)&&$imagew['name']!=""){
if( $_FILES['userfile']['type']=="image/jpeg" ||$_FILES['userfile']['type']=="image/jpg"||
$_FILES['userfile']['type']=="image/png"||$_FILES['userfile']['type']=="image/gif"){

$uploaddir = $dir."/";
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$_FILES['userfile']['name'])) {
   	$idw = rand(0000000000, 9999999999);
   	$id2 = rand(0000000000, 9999999999);
   	if($_FILES['userfile']['type']=="image/jpeg"||$_FILES['userfile']['type']=="image/jpg")$format=".jpg";
   	else if($_FILES['userfile']['type']=="image/png")$format=".png";
   	else if($_FILES['userfile']['type']=="image/gif")$format=".gif";
   	
   rename($uploaddir.$_FILES['userfile']['name'], $image = $uploaddir."wall".$idw."_".$id2.$format);
   
   mysqli_query($cn, "INSERT INTO `wall` (`id`, `uid`, `text`, `image`, `date`) 
   VALUES('', '".$id."','".$text."','".$image."','".now()."')");
   
   mysqli_query($cn,"insert into `photo` (`id`,`uid`,`dir`,`date`) 
   values('', '".$id."', '".$image."', '".now()."')");
   
   page(1);
   
  }else{
  mS(1,"Ошибка фотографии!");
  }
 }else{
 mS(1,"Формат фотграфии должен быть .jpg, .jpeg, .png или .gif");
 }
 
}else if(!isset($text)&&$imagew['name']!=""){
if( $_FILES['userfile']['type']=="image/jpeg" ||$_FILES['userfile']['type']=="image/jpg"||
$_FILES['userfile']['type']=="image/png"||$_FILES['userfile']['type']=="image/gif"){

$uploaddir = $dir;
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$_FILES['userfile']['name'])) {
   	$idw = rand(0000000000, 9999999999);
   	$id2 = rand(0000000000, 9999999999);
   	if($_FILES['userfile']['type']=="image/jpeg"||$_FILES['userfile']['type']=="image/jpg")$format=".jpg";
   	else if($_FILES['userfile']['type']=="image/png")$format=".png";
   	else if($_FILES['userfile']['type']=="image/gif")$format=".gif";
   	
   rename($uploaddir.$_FILES['userfile']['name'], $image = $uploaddir."wall".$idw."_".$id2.$format);
   
   mysqli_query($cn, "INSERT INTO `wall` (`id`, `uid`, `text`, `image`, `date`) 
   VALUES('', '".$id."','','".$image."','".now()."')");
   
   mysqli_query($cn,"insert into `photo` (`id`,`uid`,`dir`,`date`) 
   values('', '".$id."', '".$image."', '".now()."')");
   
   page(1);
   
  }else{
  mS(1,"Ошибка фотографии!");
  }
 }else{
 mS(1,"Формат фотграфии должен быть .jpg, .jpeg, .png или .gif");
 }
}else if(isset($text)&&$_FILES['userfile']['name']==""){
if($text!=""){
mysqli_query($cn,"insert into `wall`(`id`, `uid`, `image`, `text`, `date`)
values('','".$id."','','".$text."','".now()."')");
page("profile.php?id=".$id);
}else{
mS(1,"Запись не может быть пустой!");
}
}
/*if($text!=""){
mysqli_query($cn,"insert into `wall`(`id`, `uid`, `image`, `text`, `date`)
values('','".$_SESSION['id']."','','".$text."','".now()."')");
page("profile.php?id=".$id);
}else{
mS(1,"Запись не может быть пустой!");
}*/
}

if(isset($_POST['savered'])){
$text = $_POST['text'];
$did = $_POST['did'];
if($text!=""){
mysqli_query($cn,"update `wall` set text = '".$text."'");
page(1);
}else{
mS(1,"Запись не может быть пустым!");
}
}
if(isset($_POST['endred'])){
page(1);
}

if(isset($_POST['red'])){
$q=mysqli_query($cn,"select * from `wall` where uid = ".$id." and id=".$_POST['did']."");
while($w=mysqli_fetch_assoc($q)){
echo '
<div class="conteiner mr20">
<div class="row">
<form active="" method="post">
<input hidden value="'.$_POST['did'].'" name="did">
<div class="col-sm-10 col-sm-offset-2">
<div class="input-group">
					<h4>Текст:</h4>
					<h5>Можно использовать HTML код</h5>
					<textarea class="form-control" name="text" placeholder="Текст записи" rows="10" cols="60" >'.$w['text'].'</textarea><br><br>
					</div>
					<div class="btn-group">
					<input type="submit" name="savered" class="btn btn-primary" value="Сохранить" />
					</div>
					<div class="btn-group">
					<input type="submit" name="endred" class="btn btn-danger" value="Отменить" />
					</div>
					</div>
				</form>
</div>
</div>
';
}
exit();
}

if(isset($_POST['del'])){
$p=mysqli_query($cn,"select * from `wall` where id=".$_POST['did']."");
while($k=mysqli_fetch_assoc($p)){
mysqli_query($cn,"delete from `photo` where uid = ".$id." and dir = '".$k['image']."'");
mysqli_query($cn,"delete from `wall` where uid = ".$id." and id =".$_POST['did']."");
unlink($k['image']);
page("profile.php?id=".$id);
}
exit();
}

if(isset($_POST['savereport'])){
mysqli_query($cn,"insert into `report`(`id`, `wid`, `tid`, `text`, `date`, `which`) 
VALUES ('','".$_SESSION['id']."','".$id."','".$_POST['text']."','".now()."','".$_POST['id']."')");
mS(2,"Ваша жалоба отправлена");
exit();
}
if(isset($_POST['report'])){
echo'
<div class="conteiner">
<div class="row mr20">
<div class="col-sm-8 col-sm-offset-2">
<form action="" method="post">
<input hidden name="id" value="'.$_POST['did'].'">
		<div class="input-group">
		<h4>Написать жалобу на запись:</h4>
		<h5>Можно использовать HTML код</h5>
		<textarea class="form-control" name="text" placeholder="Напишите вашу жалобу" rows="3" cols="100" ></textarea>
		</div>
		<div class="btn-group">
		<input type="submit" name="savereport" class="btn btn-primary" value="Отправить" />
		</div>
		</form>
</div>
</div>
</div>
';
exit();
}

if(isset($_POST['savereportuser'])){
mysqli_query($cn,"insert into `report`(`id`, `wid`, `tid`, `text`, `date`, `which`) 
VALUES ('','".$_SESSION['id']."','".$id."','".$_POST['text']."','".now()."','Пользователь')");
mS(2,"Ваша жалоба отправлена");
exit();
}
if(isset($_POST['reportuser'])){
echo'
<div class="conteiner">
<div class="row mr20">
<div class="col-sm-8 col-sm-offset-2">
<form action="" method="post">
		<div class="input-group">
		<h4>Написать жалобу на пользователя:</h4>
		<h5>Можно использовать HTML код</h5>
		<textarea class="form-control" name="text" placeholder="Напишите вашу жалобу" rows="3" cols="100" ></textarea>
		</div>
		<div class="btn-group">
		<input type="submit" name="savereportuser" class="btn btn-primary" value="Отправить" />
		</div>
		</form>
</div>
</div>
</div>
';
exit();
}
?>

<div class="conteiner">
	<div class="row mr10">
		<div class="col-sm-3">
			<h4><?php echo $nick;?></h4>
			
			<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">•••</button>
			<ul class="dropdown-menu centered" role="menu" aria-labelledby="dLabel" >
			<form action="" method="post">
			<div class="btn-group">
			<input type="submit" name="reportuser" class="btn btn-default" value="Пожаловаться">
			</div>
			</form>
			</ul>
			</div>
			
		</div>
		<div class="col-sm-9 mr10">
			<div class="fa fa-comments" id="statusjs"> <?php echo $status; ?></div>
			<div style="display:none;" class="fa fa-comments" id="statusjs1">
				<form action="" method="post">
					<div class="input-group">
					<h4>Статус:</h4>
					<h5>Можно использовать HTML код</h5>
					<textarea class="form-control" name="text" placeholder="Статус" rows="3" cols="50" ><?php 
					if($status!="Нажмите чтобы изменить статус"){
					echo $status;
					}
					?></textarea><br><br>
					</div>
					<div class="btn-group">
					<input type="submit" name="status" class="btn btn-primary" value="Сохранить" />
					</div>
				</form>
				<div class="btn-group">
				<a class="btn btn-danger" onclick="closeStatus()" >Отмена</a>
				</div>
			</div>
			
			
			
			
			
			<?php if($id==$_SESSION['id']||$_SESSION['perm']>0):?>
			<div class="addwall">
			<form action="" method="post" enctype="multipart/form-data">
			<div class="input-group">
			<h4>Добавить новую запись:</h4>
			<h5>Можно использовать HTML код</h5>
			<textarea class="form-control" name="text" placeholder="Что нового?" rows="3" cols="100" ></textarea>
			</div>
						<input type="file" name="userfile" class="btn btn-primary" >
			<div class="btn-group mr20">
			<input type="submit" name="wall" class="btn btn-primary" value="Добавить" />
			</div>
			</form>
			</div>
			
			<?php endif; ?>
			
			
			
			</div>
			<div class="mr20">
				<div class="wall">
					<?php 
					$j=mysqli_query($cn,"select * from `wall` where uid = ".$id." order by date desc");
					$k=mysqli_num_rows($j);
					if($k==0)echo"Стена пустая";
					while($v=mysqli_fetch_assoc($j)):
					$did = $v['id'];
					$img = $v['image'];
					$uid = $v['uid'];
					$text = $v['text'];
					$date = $v['date'];
					$c=mysqli_query($cn,"select * from `users` where id = ".$uid."");
					while($d=mysqli_fetch_assoc($c)):?>
					
					<div class="new">
						<div class="avt">
						<a href="profile.php?id=<?php echo $id;?>"><?php echo $d['login'];?></a>
						
						<div class="btn-group dropup">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">•••</button>
						<ul class="dropdown-menu centered" role="menu" aria-labelledby="dLabel">
						<form action="" method="post">
						<input hidden value="<?php echo $id;?>" name="id">
						<input hidden value="<?php echo $did;?>" name="did">
						<?php if($id==$_SESSION['id']):?>
						<div class="btn-group mr10">
						<li><input type="submit" class="btn btn-primary" name="red" value="Редактировать"></li>
						</div>
						<div class="btn-group">
						<li><input type="submit" class="btn btn-danger" name="del" value="Удалить"></li>
						</div>
						<li class="divider"></li>
						<?php endif;?>
						<div class="btn-group">
						<li><input type="submit" class="btn btn-default" name="report" value="Пожаловаться"></li>
						</div>
						</form>
						</ul>
						</div>
						
						</div>
						<div class="text">
						<div class="panelright" style="color:silver;">
						<?php echo $date;?>
						</div>
						<?php echo $text;?>
					 <div class="image">
						<?php 
						$f=mysqli_query($cn,"select * from `photo` where dir = '".$img."'");
						while($t=mysqli_fetch_assoc($f)):
						?>
						<a href="photo.php?photo=<?php echo $t['id']; ?>&pid=<?php echo $t['uid']; ?>">
						<img src="<?php echo $img; ?>"></a>
						<?php endwhile; ?>
						</div>
					 </div>
					</div>
					
					<?php endwhile;
					endwhile;
					?>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php if($id==$_SESSION['id']): ?>
<script type="text/javascript">
function closeStatus(){
$("#statusjs").show(1);
stat1.style.display = "none";
}
var stat = document.getElementById("statusjs");
var stat1 = document.getElementById("statusjs1");
stat.onclick = function(){
$("#statusjs").hide(1);
stat1.style.display = "block";
}
</script>
<?php endif;?>
<?php endwhile;
requirejs();
?>