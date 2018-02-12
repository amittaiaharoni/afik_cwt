<?php
    $contact_page = $this->data->infoPage->get_by_id("4");
?>
<div class="white_cont">
	<div class="title">
		<h1>צוות CWT  זמין  לרשותכם לכל שאלה.</h1>
	</div>
    <div class="contact_address">

    </div>
	<div id="contact_form" class="cf">
        <?php if ($contact_page->text){ ?>
        <p><?php echo $contact_page->text;?></p>
        <?php } ?>
		<form action="" method="post">
			<div class="half">
				<input type="text" name="name" id="name" placeholder="שם"/>
                <input type="text" name="phone" id="phone" placeholder="טלפון"/>
				<input type="text" name="email" id="email" placeholder="אימייל"/>
			</div>
			<div class="half">
				<textarea type="text" name="text" id="text" placeholder="טקסט" rows="5"></textarea>
			</div>
			<button name="send_contact">שלח פרטים</button>
		</form>
	</div>
	<div id="contact_map" class="cf">
	   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3386.129769672317!2d34.943023684839204!3d31.93023398123591!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1502cb8fea5f1017%3A0x89e4a1c762af5c54!2z15TXktek158gODQsINeS157XlteV!5e0!3m2!1siw!2sil!4v1500882316460" width="100%" height="380" frameborder="0" style="border:0" allowfullscreen></iframe>
	</div>
</div>


