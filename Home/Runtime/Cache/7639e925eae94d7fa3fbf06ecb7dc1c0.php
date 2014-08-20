<?php if (!defined('THINK_PATH')) exit();?><ul class="breadcrumb well">
	<li><a href="#">首页</a> <span class="divider">/</span></li>
	<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$index): $mod = ($i % 2 );++$i;?><li><a href="#"><?php echo ($data); ?></a> <span class="divider">/</span></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>