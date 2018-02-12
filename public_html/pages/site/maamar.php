<div id="contain_side" class="article_holder">
<?
if(!empty($_GET['id'])){
    $icat = $this->data->infoCat->get_by_id((int)$_GET['id']);
    if(!empty($icat)){
        $pages = $icat->get_pages();
        show_pdfs($pages);
    }
	
}
else{
    $icats = $this->data->infoCat->get_by_column('parent_cat_id',4);
    $pages = $icats[0]->get_pages();
    show_pdfs($pages);  
}
function show_pdfs($pages){
	if(!empty($pages)){
		foreach($pages as $page){
			if(empty($page->pdf_file))
				continue;
				?>
					<div class="pdf_file_holder">
				<div class="pdf_file">
					<a href="<?=site_config::get_value('upload_files_folder').$page->pdf_file?>">
					<img src="<?=!empty($page->image)?site_config::get_value('upload_images_folder').$page->image:'upload/images/14962978061.jpg'?>" alt="" />
					<span><img src="pics/pdf.png" alt=""></span>
					<h2><?=$page->name?></h2>
					</a>
				</div>
			  </div>
				<?
		}
	}
}
?>
</div>