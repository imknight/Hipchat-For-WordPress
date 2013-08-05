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
      <?php if(!empty($auth_token)) :?>
      <tr>
        <th>
          <label for="room_name">Post Types</label>
          <select name="post_types_notify">
          <?php foreach ( $post_types  as $post_type ) :?>
          <option value="<?php echo $post_type ?> <?php echo ( $post_type==$selected ) ? 'selected':''?>"><?php echo ucfirst( $post_type ); ?></option>
          <?php endforeach ?>
          </select>

        </th>
        <td>
          <input name="post_types_notify_room" type="text" value="<?php echo $selectedroom ?>" class="regular-text">
          <span class="description">
            Name of the room to send messages to for this post type.
          </span>
        </td>
      </tr>
    <?php endif; ?>
    </table>
    <p class="submit">
      <input type="submit" name="Submit" class="button-primary"
             value="Save Changes">
    </p>
  </form>
</div>
