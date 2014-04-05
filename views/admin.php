<style type="text/css" media="screen">
#icon-settings-hipchat {
  background: transparent url(/wp-content/plugins/hipchat/logo.png) no-repeat top left;
}
</style>

<div class="wrap">
  <div id="icon-settings-hipchat" class="icon32"><br/></div>
  <h2>HipChat For WordPress Plugin Settings</h2>

  <p>This plugin will send a HipChat message whenever a new content is published. Please Generate "Notification" type of HipChat API</p>

  <?php if ( $updated ): ?>
  <div class="updated"><p><?php echo $updated ?></p></div>
<?php endif; ?>

<?php if ( $error ): ?>
  <div class="error"><p><?php echo $error ?></p></div>
<?php endif; ?>

<form name="hipchat" method="post" action="">
  <table class="form-table">
    <tr>
      <th>
        <label for="auth_token">Auth Token</label>
      </th>
      <td>
        <input name="auth_token" type="text" id="auth_token"
        value="<?php echo $auth_token ?>" class="regular-text">
        <span class="description">
          A HipChat
          <a href="http://www.hipchat.com/group_admin/api" target="_blank">
            API token</a>.
          </span>
        </td>
      </tr>
      <tr>
        <th>
          <label for="room_name">Room Name</label>
        </th>
        <td>
          <input name="room" type="text" id="room" value="<?php echo $room ?>" class="regular-text">
          <span class="description">
            Name of the room to send messages to test your API token.
          </span>
        </td>
      </tr>
      <tr>
        <th>
          <label for="notify_status">Notify When</label>
        </th>
        <td>
          <label><input type="radio" name="notify_status" value="all" <?php checked( $status, 'all') ?>> All Status</label>
          <label><input type="radio" name="notify_status" value="publish" <?php checked( $status, 'publish') ?>> Publish Only</label>
          <span class="description">
            Send notify when every status changes or only when publishing only
          </span>
        </td>
      </tr>
    </table>
<br>
<?php if ( !empty( $auth_token ) ) :?>
  <div style="width:60%">
   <table id="notifytable" class="widefat" width="100%">
    <tbody>
    <tr>
      <th>Post Type</th>
      <th width="70%">Message ( customize the message ) </th>
      <th><a class="button" id="add-row" href="#">+</a></th>
    </tr>
  <?php if(!empty($notify)) :?>
    <?php foreach ($notify['post_type'] as $i => $type ) : ?>
    <tr>
      <td>
          <select name="notify_type[]" class="widefat">
            <?php foreach ( $post_types  as $post_type ) :?>
              <option value="<?php echo $post_type ?>" <?php echo ($post_type==$type) ? 'SELECTED':''?>><?php echo ucfirst( $post_type ); ?></option>
            <?php endforeach ?>
          </select>
      </td>
      <td><input type="text" name="notify_msg[]" value="<?php echo $notify['post_msg'][$i]?>" class="widefat" /></td>
      <td><a class="button remove-row" href="#">-</a></td>
    </tr>
    <?php endforeach; ?>
  <?php endif; ?>

  <tr class="empty-row screen-reader-text">
    <td>
        <select name="notify_type[]" class="widefat">
          <?php foreach ( $post_types  as $post_type ) :?>
            <option value="<?php echo $post_type ?>"><?php echo ucfirst( $post_type ); ?></option>
          <?php endforeach ?>
        </select>
    </td>
    <td><input type="text" name="notify_msg[]" value="%title% is added" class="widefat" /></td>
    <td><a class="button remove-row" href="#">-</a></td>
  </tr>
  </tbody>
  </table>
  </div>
<?php endif; ?>

<p class="submit">
  <input type="submit" name="Submit" class="button-primary"
  value="Save Changes">
</p>
</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {

  $('#add-row').on('click', function() {
    var row = $('.empty-row.screen-reader-text').clone(true);
    row.removeClass('empty-row screen-reader-text');
    row.insertBefore('#notifytable tbody>tr:last');
    return false;
  });
  $('.remove-row').on('click', function() {
    $(this).parents('tr').remove();
    return false;
  });

});
  </script>
