<?php
/*
Plugin Name: Create Eye Catch For Classic
Description: Classicエディタでアイキャッチ画像を簡単に生成することができます。
Author: nove-b
Author URI: https://github.com/nove-b
Version: 1.3
*/

// phpファイルのURLに直接アクセスされても中身見られないようにする
if (!defined('ABSPATH')) exit;


// 設定メニューの追加
function cecfc_CreateEyeCatchMenu()
{
  add_menu_page(
    __('Create Eye Catchの設定', 'textdomain'), // ページタイトル
    'Create Eye Catch',  // メニュータイトル
    'manage_options', // 
    'create-eye-catch',  // メニューslug
    'cecfc_CreateEyeCatchPage',  // 実行する関数
    'dashicons-chart-pie',  // メニューに表示するアイコン
    6 // メニューの表示位置
  );
  register_setting('myoption-group', 'eyeCatchUrl');
  register_setting('myoption-group', 'totalLine');
  register_setting('myoption-group', 'oneLineTextLength');
  register_setting('myoption-group', 'fontSize');
  register_setting('myoption-group', 'topMargin');
  register_setting('myoption-group', 'leftMargin');
  register_setting('myoption-group', 'fontColor');
}
add_action('admin_menu', 'cecfc_CreateEyeCatchMenu');

// 設定画面の追加

/**
 * メニューページの中身を作成
 */
function cecfc_CreateEyeCatchPage()
{
  // 権限チェック.
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
?>
  <div class="wrap">
    <h1>アイキャッチ画像の設定</h1>
    <form method="post" action="options.php">
      <?php settings_fields('myoption-group'); ?>
      <?php do_settings_sections('myoption-group'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">背景画像のURL</th>
          <td>
            <input type="text" oninput="cecfc_changeEyeCatchUrl(this)" name="eyeCatchUrl" value="<?php echo esc_attr(get_option('eyeCatchUrl')); ?>" size="50" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">１行の文字量</th>
          <td>
            <input type="number" min="1" oninput="cecfc_changeOneLine(this)" name="oneLineTextLength" value="<?php echo esc_attr(get_option('oneLineTextLength')); ?>" size="50" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">文字サイズ</th>
          <td>
            <input type="number" min="1" oninput="cecfc_changeFontSize(this)" name="fontSize" value="<?php echo esc_attr(get_option('fontSize')); ?>" size="50" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">最大行</th>
          <td>
            <input type="number" min="1" oninput="cecfc_changeTotalLine(this)" name="totalLine" value="<?php echo esc_attr(get_option('totalLine')); ?>" size="50" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">文字色</th>
          <td>
            <input type="color" oninput="cecfc_changeFontColor(this)" name="fontColor" value="<?php echo esc_attr(get_option('fontColor')); ?>" size="50" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">上の空き幅</th>
          <td>
            <input type="number" min="1" oninput="cecfc_changeTopMargin(this)" name="topMargin" value="<?php echo esc_attr(get_option('topMargin')); ?>" size="50" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">左の空き幅</th>
          <td>
            <input type="number" min="1" oninput="cecfc_changeLeftMargin(this)" name="leftMargin" value="<?php echo esc_attr(get_option('leftMargin')); ?>" size="50" />
          </td>
        </tr>
      </table>
      <h1>PREVIEW</h1>
      <canvas id="preview" style="max-width: 786px;"></canvas>
      <?php submit_button(); ?>
    </form>
  </div>
  <script>
    // Font Config
    let cecfc_oneLineTextLength = <?php echo esc_attr(get_option('oneLineTextLength') == '' ? '14' : get_option('oneLineTextLength')); ?>;
    let cecfc_fontSize = <?php echo esc_attr(get_option('fontSize') == '' ? 70 : get_option('fontSize')); ?>;
    const cecfc_lineHeight = 1.75;
    let cecfc_fontColor = `<?php echo esc_attr(get_option('fontColor') == '' ? "#000000" : get_option('fontColor')); ?>`
    let cecfc_totalLine = `<?php echo esc_attr(get_option('totalLine') == '' ? "3" : get_option('totalLine')); ?>`

    // Position Config

    let cecfc_topMargin = <?php echo esc_attr(get_option('topMargin') == '' ? '220' : get_option('topMargin')); ?>;
    let cecfc_leftMargin = <?php echo esc_attr(get_option('leftMargin') == '' ? '100' : get_option('leftMargin')); ?>;

    const cecfc_previewCanvas = document.getElementById('preview')
    const cecfc_ctxPreview = cecfc_previewCanvas.getContext('2d')
    const cecfc_previewImage = new Image();
    cecfc_previewImage.src = '<?php echo esc_attr(get_option('eyeCatchUrl') == '' ? plugins_url('sample/sample.png', __FILE__) : get_option('eyeCatchUrl'));  ?>';
    cecfc_previewImage.onload = () => {

      // Canvas のサイズを変更する
      cecfc_previewCanvas.width = cecfc_previewImage.width
      cecfc_previewCanvas.height = cecfc_previewImage.height
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }

    // eyeCatchUrlを変更した時
    const cecfc_changeEyeCatchUrl = (el) => {
      cecfc_previewImage.src = el.value
      cecfc_previewImage.onload = () => {
        // Canvas のサイズを変更する
        cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
        cecfc_previewCanvas.width = cecfc_previewImage.width
        cecfc_previewCanvas.height = cecfc_previewImage.height
        cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
        cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
      }
    }

    // 一行の文字量を変更した時
    const cecfc_changeOneLine = (el) => {
      cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_oneLineTextLength = el.value;
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }
    // 最大行を変更した時
    const cecfc_changeTotalLine = (el) => {
      cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_totalLine = el.value;
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }

    // 文字サイズを変更した時
    const cecfc_changeFontSize = (el) => {
      cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_fontSize = el.value;
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }

    // 文字色を変更した時
    const cecfc_changeFontColor = (el) => {
      cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_fontColor = el.value;
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }


    // Margin Topを変更した時
    const cecfc_changeTopMargin = (el) => {
      cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_topMargin = Number(el.value);
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }
    // Margin Leftを変更した時
    const cecfc_changeLeftMargin = (el) => {
      cecfc_ctxPreview.clearRect(0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_leftMargin = Number(el.value);
      cecfc_ctxPreview.drawImage(cecfc_previewImage, 0, 0, cecfc_previewCanvas.width, cecfc_previewCanvas.height);
      cecfc_drawFont('統一感のあるアイキャッチ画像を、ブログタイトルから簡単に作成⚡')
    }


    // 入力されたタイトルを描画する関数
    const cecfc_drawFont = (el) => {
      let title = el.substring(0, cecfc_oneLineTextLength * cecfc_totalLine)

      // ↑で生成されたタイトルの文字量が記入されたタイトルより少ない時
      if (title.length < el.length) {
        title = title.substr(0, title.length - 1) + '…';
      }

      cecfc_ctxPreview.font = `bold ${cecfc_fontSize}px 'Roboto', 'Noto Sans JP'`;
      cecfc_ctxPreview.fillStyle = cecfc_fontColor;
      const inputTextArray = title.split('')

      //出力用の配列を用意
      // https://kinocolog.com/javascript_canvas_br/
      let aryRow = [];
      aryRow[0] = '';
      let row_cnt = 0;

      //入力1文字毎にループ　改行コードもしくは折り返しで配列の添え字を足す
      for (let i = 0; i < inputTextArray.length; i++) {
        let text = inputTextArray[i];
        if (aryRow[row_cnt].length >= cecfc_oneLineTextLength) {
          row_cnt++;
          aryRow[row_cnt] = '';
        }
        if (text == "\n") {
          row_cnt++;
          aryRow[row_cnt] = '';
          text = '';
        }
        aryRow[row_cnt] += text;
      }

      //文字の表示　y軸とx軸をループする
      for (let i = 0; i < aryRow.length; i++) {
        aryStr = aryRow[i].split('');
        for (let j = 0; j < aryStr.length; j++) {
          cecfc_ctxPreview.fillText(aryStr[j], (j * cecfc_fontSize) + cecfc_leftMargin, (i * cecfc_fontSize * cecfc_lineHeight) + cecfc_topMargin);
        }
      }
    }
  </script>
<?php
}

// メインの処理
add_action('admin_footer-post-new.php', 'cecfc_DisplayCreateEyeCatch');
add_action('admin_footer-post.php', 'cecfc_DisplayCreateEyeCatch');
function cecfc_DisplayCreateEyeCatch()
{
?>
  <script>
    // Font Config
    let cecfc_oneLineTextLength = <?php echo esc_attr(get_option('oneLineTextLength') == '' ? '14' : get_option('oneLineTextLength')); ?>;
    let cecfc_fontSize = <?php echo esc_attr(get_option('fontSize') == '' ? 70 : get_option('fontSize')); ?>;
    const cecfc_lineHeight = 1.75;
    let cecfc_fontColor = `<?php echo esc_attr(get_option('fontColor') == '' ? "#000000" : get_option('fontColor')); ?>`
    let cecfc_totalLine = `<?php echo esc_attr(get_option('totalLine') == '' ? "3" : get_option('totalLine')); ?>`

    // Position Config

    let cecfc_topMargin = <?php echo esc_attr(get_option('topMargin') == '' ? '220' : get_option('topMargin')); ?>;
    let cecfc_leftMargin = <?php echo esc_attr(get_option('leftMargin') == '' ? '100' : get_option('leftMargin')); ?>;


    const cecfc_insertTarget = document.getElementById('postimagediv');
    const cecfc_titleInput = document.getElementById('title');

    cecfc_insertTarget.insertAdjacentHTML('afterend',
      `<canvas style="width:100%" class="canvas" id="canvasBackground" width="788px" height="486px"></canvas>
          <a id="saveEyeCatch"  style="margin-top:10px" class="button button-primary button-large">保存する</a>
          <div id=result></div>
          `);

    const cecfc_saveEyeCatch = document.getElementById('saveEyeCatch')
    const cecfc_canvasGround = document.getElementById('canvasBackground')
    const cecfc_ctxGround = cecfc_canvasGround.getContext('2d')
    const cecfc_nove = new Image();
    cecfc_nove.src = '<?php echo esc_attr(get_option('eyeCatchUrl') == '' ? plugins_url('sample/sample.png', __FILE__) : get_option('eyeCatchUrl'));  ?>';
    cecfc_nove.onload = () => {
      // Canvas のサイズを変更する
      cecfc_canvasGround.width = cecfc_nove.width
      cecfc_canvasGround.height = cecfc_nove.height

      cecfc_ctxGround.drawImage(cecfc_nove, 0, 0, cecfc_canvasGround.width, cecfc_canvasGround.height);
      cecfc_drawFont(cecfc_titleInput.value)
    }
    cecfc_nove.crossOrigin = "anonymous";

    cecfc_titleInput.addEventListener('change', () => {
      cecfc_ctxGround.clearRect(0, 0, cecfc_canvasGround.width, cecfc_canvasGround.height);
      const cecfc_nove = new Image();
      cecfc_nove.src = '<?php echo esc_attr(get_option('eyeCatchUrl') == '' ? plugins_url('sample/sample.png', __FILE__) : get_option('eyeCatchUrl'));  ?>';
      cecfc_nove.onload = () => {
        // Canvas のサイズを変更する
        cecfc_canvasGround.width = cecfc_nove.width
        cecfc_canvasGround.height = cecfc_nove.height

        cecfc_ctxGround.drawImage(cecfc_nove, 0, 0, cecfc_canvasGround.width, cecfc_canvasGround.height);
        cecfc_drawFont(cecfc_titleInput.value)
      }
      cecfc_nove.crossOrigin = "anonymous";
    })

    // 入力されたタイトルを描画する関数
    const cecfc_drawFont = (el) => {
      const cecfc_ctxGround = cecfc_canvasGround.getContext('2d');
      let title = el

      // フォントサイズの指定（半角、全角の幅を考慮）
      cecfc_ctxGround.font = `bold ${cecfc_fontSize}px 'Roboto', 'Noto Sans JP'`;

      // フォントカラーの指定
      cecfc_ctxGround.fillStyle = cecfc_fontColor;

      const inputTextArray = title.split('');

      let rowText = '';
      let rowArray = [];

      for (let i = 0; i < inputTextArray.length; i++) {
        const metrics = cecfc_ctxGround.measureText(rowText + inputTextArray[i]);

        if (metrics.width > cecfc_oneLineTextLength * cecfc_fontSize) {
          rowArray.push(rowText);
          rowText = '';
        }

        rowText += inputTextArray[i];
      }

      rowArray.push(rowText);


      const lastLine = rowArray[cecfc_totalLine - 1]
      const lastLinveOver = rowArray[cecfc_totalLine]
      if(lastLine && lastLinveOver) {
        rowArray[cecfc_totalLine - 1] = replaceLastChar(lastLine, '...');
      }
      for (let i = 0; i < rowArray.length; i++) {
        if (i < cecfc_totalLine) {

          const text = rowArray[i];
          const y = (i * cecfc_fontSize * cecfc_lineHeight) + cecfc_topMargin;

          // テキストの描画
          cecfc_ctxGround.fillText(text, cecfc_leftMargin, y);
        }
      }
    };

    cecfc_saveEyeCatch.addEventListener('click', () => {
      const base64 = canvasBackground.toDataURL("image/jpeg");
      cecfc_saveEyeCatch.href = base64;
      cecfc_saveEyeCatch.download = `${new Date().getTime()}.jpg`;
      //  altに使用するため、titleをcopy
      title.select();
      document.execCommand("copy");
    })


    function replaceLastChar(inputString, newChar) {
      if (inputString.length > 0) {
        // 最後の文字を取得
        const lastChar = inputString.charAt(inputString.length - 1);

        // 最後の文字を置換したい文字に変更
        const modifiedString = inputString.slice(0, -1) + newChar;

        return modifiedString;
      } else {
        return inputString; // 空文字列の場合は変更なし
      }
    }
  </script>
<?php
}
