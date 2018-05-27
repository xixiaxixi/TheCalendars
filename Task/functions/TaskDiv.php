<?php
function task_div($proj, $task)
{
    $deadline = strtotime($task['deadline']);
    $date = date("Y-m-d", $deadline);
    $time = date("h:i:s", $deadline);

    ?>
    <div class='task-div' id="<?php echo "{$proj['id']}" . ' ' . "{$task['id']}" ?>">
        <div class='task-head'>
            <div class="task-title"><?php echo "{$task['name']}" ?></div>
            <div class="button-set">
                <button class='opera-button alter-button'>Alter</button>
                <button class='opera-button finish-button'>Finish</button>
                <button class='opera-button delete-button'>Delete</button>
            </div>
        </div>
        <form class='task-form' style='display:none'>
            <fieldset class="input-body">
                <legend class="input-title">Name&Intro</legend>
                <input class='task-name' type='text' name='task_name'
                       value='<?php echo "{$task['name']}" ?>'><br>
                <textarea class='task-descript'
                          name='body'><?php echo "{$task['description']}" ?></textarea>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Deadline</legend>
                <input class='ddl' type='date' name="date" value='<?php echo "$date" ?>'><br>
                <input class='ddl' type='time' name="time" value='<?php echo "$time" ?>'><br>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Participants</legend>
                <?php
                foreach ($proj['participants'] as $id => $name)
                {
                    if (!isset($name) || empty($name)) $name = $id;
                    if (array_key_exists($id, $task['participants']))
                        echo "<input type='checkbox' name='member[]' value='$id' checked='checked'/>";
                    else
                        echo "<input type='checkbox' name='member[]' value='$id'/>";
                    echo "<span class='checkbox-label for$id'>$name</span><br>";

                }
                ?>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Formers</legend>
                <?php
                foreach ($proj['tasks'] as $id => $task_data)
                {
                    if ($id != $task['id'])//不能选择自己为前继
                    {
                        if (array_key_exists($id, $task['formers']))
                            echo "<input type='checkbox' name='task[]' value='$id' checked='checked'/>";
                        else
                            echo "<input type='checkbox' name='task[]' value='$id'/>";
                        echo "<span class='checkbox-label for$id'>{$task_data['name']}</span><br>";
                    }
                }
                ?>
            </fieldset>
            <div class="button-set" style="bottom: 8px">
                <button type="button" class='opera-button update-button'>OK</button>
                <button type="button" class='opera-button cancel-button'>Cancel</button>
            </div>
        </form>
    </div>
    <?php
} ?>

<?php
function add_task_div($proj)
{
    ?>
    <div class='task-div' id="new-task" style="display: none">
        <div class='task-head'>
            <div class="task-title">New Task</div>
        </div>
        <form class='task-form'>

            <fieldset class="input-body">
                <legend class="input-title">Name&Intro</legend>
                <input class='task-name' type='text' name='task_name'><br>
                <textarea class='task-descript' name='body'></textarea><br>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Deadline</legend>
                <input class='ddl' type='date' name="date" value="<?php echo date("Y-m-d")?>"><br>
                <input class='ddl' type='time' name="time" value="<?php echo date("H:i:s")?>"><br>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Participants</legend>
                <?php
                foreach ($proj['participants'] as $id => $name)
                {
                    if (!isset($name) || empty($name)) $name = $id;
                    echo "<input type='checkbox' name='member[]' value='$id'/>";
                    echo "<span class='checkbox-label for$id'>$name</span><br>";
                }
                ?>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Formers</legend>
                <?php
                foreach ($proj['tasks'] as $id => $task)
                {
                    echo "<input type='checkbox' name='task[]' value='$id'/>";
                    echo "<span class='checkbox-label for$id'>{$task['name']}</span><br>";
                }

                ?>
            </fieldset>
            <div class="button-set" style="bottom: 8px">
                <button type="button" class='opera-button add-button' id='<?php echo "{$proj['id']}" ?>'>OK</button>
                <button type="button" class='opera-button cancel-button'>Cancel</button>
            </div>
        </form>
    </div>
    <?php
} ?>

<?php
function viewonly_task_div($proj, $task)
{
    $deadline = strtotime($task['deadline']);
    $date = date("Y-m-d", $deadline);
    $time = date("h:i:s", $deadline);

    ?>
    <div class='task-div' id="<?php echo "{$proj['id']}" . ' ' . "{$task['id']}" ?>">
        <div class='task-head'>
            <div class="task-title"><?php echo "{$task['name']}" ?></div>
            <div class="button-set">
                <button class='opera-button alter-button'>View</button>
            </div>
        </div>
        <form class='task-form' style='display:none'>

            <fieldset class="input-body">
                <legend class="input-title">Name&Intro</legend>
                <input readonly class='task-name' type='text' name='task_name'
                       value='<?php echo "{$task['name']}" ?>'><br>
                <textarea readonly class='task-descript'
                          name='body'><?php echo "{$task['description']}" ?></textarea>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Deadline</legend>
                <input readonly class='ddl' type='date' name="date" value='<?php echo "$date" ?>'><br>
                <input readonly class='ddl' type='time' name="time" value='<?php echo "$time" ?>'><br>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Participants</legend>
                <?php
                foreach ($proj['participants'] as $id => $name)
                {
                    if (!isset($name) || empty($name)) $name = $id;
                    if (array_key_exists($id, $task['participants']))
                        echo "<span class='checkbox-label-readonly'>$name</span><br>";
                }
                ?>
            </fieldset>

            <fieldset class="input-body">
                <legend class="input-title">Formers</legend>
                <?php
                foreach ($proj['tasks'] as $id => $task_data)
                {
                    if ($id != $task['id'])//不能选择自己为前继
                    {
                        if (array_key_exists($id, $task['formers']))
                            echo "<span class='checkbox-label-readonly'>{$task_data['name']}</span><br>";
                    }
                }
                ?>
            </fieldset>
            <div class="button-set" style="bottom: 8px">
                <button type="button" class='opera-button cancel-button'>Cancel</button>
            </div>
        </form>
    </div>
    <?php
} ?>


