<?php
function proj_div($proj)
{
    ?>
    <div class="proj-display">
        <div class="proj-name"><?php echo"{$proj['name']}" ?></div>
        <div class="proj-container"  onclick="window.open('Task/Project.php?proj_id=<?php echo"{$proj['id']}" ?>')">
            创建人：
            <?php
            $maker_name=empty($proj['participants'][$proj['maker']])?$proj['maker']:$proj['participants'][$proj['maker']];
            echo"$maker_name";
            ?><br>
            描述：<br><?php echo"{$proj['description']}" ?><br>
            参与者：<br>
            <?php
            foreach ($proj['participants'] as $user_id=>$name)
            {
                echo (!isset($name)||empty($name)?$user_id:$name);
                echo "<br>";
            }
            ?><br>
        </div>
        <div class="button-set-div">
            <div class="button-set">
                <button class="opera-button" onclick="window.open('Task/new_group.php?action=alter&proj_id=<?php echo "{$proj['id']}" ?>')">Alter</button>
                <button class="opera-button delete-button" id="<?php echo"{$proj['id']}" ?>">Delete</button>
            </div>
        </div>
    </div>
    <?php
}
?>

<?php
function viewonly_proj_div($proj)
{
    ?>
    <div class="proj-display">
        <div class="proj-name"><?php echo"{$proj['name']}" ?></div>
        <div class="proj-container"  onclick="window.open('Task/Project.php?proj_id=<?php echo"{$proj['id']}" ?>')">
            创建人：<?php echo"{$proj['maker']}" ?><br>
            描述：<br><?php echo"{$proj['description']}" ?><br>
            参与者：<br>
            <?php
            foreach ($proj['participants'] as $user_id=>$name)
            {
                echo (!isset($name)||empty($name)?$user_id:$name);
                echo "<br>";
            }
            ?><br>
        </div>
        <div class="button-set-div">
            <div class="button-set">
                <button class="opera-button" onclick="window.open('Task/Project.php?proj_id=<?php echo"{$proj['id']}" ?>')">View</button>
            </div>
        </div>
    </div>
    <?php
}
?>

