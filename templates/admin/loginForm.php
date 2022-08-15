<?php include "templates/include/header.php" ?>

      <form action="admin.php?action=login" method="post" style="width: 50%;">
        <input type="hidden" name="login" value="true" />
        <h3 style="text-align: center;">Авторизация</h3>
<?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>

        <ul>

          <li>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required autofocus maxlength="20" />
          </li>

          <li>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required maxlength="20" />
          </li>

        </ul>

        <div class="buttons">
          <input type="submit" name="login" value="Вход" />
        </div>
        <br>
        <div style="text-align: center;">
        <a href="admin.php?action=registration">Не заргистрированы?</a>
        </div>

      </form>

<?php include "templates/include/footer.php" ?>

