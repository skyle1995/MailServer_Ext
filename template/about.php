<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">关于我们</h3>
            </div>
            <div class="panel-body">
                <p><?php echo isset($tips) ? $tips : ''; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">联系我们</h3>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php if (isset($email) && !empty($email)): ?>
                    <li class="list-group-item"><span class="glyphicon glyphicon-envelope"></span> 邮箱: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></li>
                    <?php endif; ?>
                    
                    <?php if (isset($phone) && !empty($phone)): ?>
                    <li class="list-group-item"><span class="glyphicon glyphicon-phone"></span> 电话: <?php echo $phone; ?></li>
                    <?php endif; ?>
                    
                    <?php if (isset($address) && !empty($address)): ?>
                    <li class="list-group-item"><span class="glyphicon glyphicon-home"></span> 地址: <?php echo $address; ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>