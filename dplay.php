<html>
<head>
<title>DPlayer-p2p</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta http-equiv="content-language" content="zh-CN" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta name="referrer" content="never" />
<meta name="renderer" content="webkit" />
<meta name="msapplication-tap-highlight" content="no" />
<meta name="HandheldFriendly" content="true" />
<meta name="x5-page-mode" content="app" />
<meta name="Viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<style type="text/css">
    body,html{width:100%;height:100%;background:#000;padding:0;margin:0;overflow-x:hidden;overflow-y:hidden}
    *{margin:0;border:0;padding:0;text-decoration:none}
    #stats{position:fixed;top:5px;left:10px;font-size:12px;color:#fdfdfd;z-index:2147483647;text-shadow:1px 1px 1px #000, 1px 1px 1px #000}
    #dplayer{position:inherit}
</style>
</head>

<body>
<div id="dplayer"></div>
<div id="stats"></div>
<script src="https://cdn.jsdelivr.net/npm/cdnbye@latest"></script>
<script src="https://cdn.jsdelivr.net/gh/xzc08218/cdn@master/js/DPlayer.min.js"></script> 
<!-- 以上 DPlayer 为修改版Lite https://github.com/kn007/DPlayer-Lite -->

<!-- <script src="https://cdn.bootcdn.net/ajax/libs/dplayer/1.26.0/DPlayer.min.js"></script> -->
<script>
    var url = '<?php echo $_GET['url'];?>';
    var isQQBrowser = /MQQBrowser/i.test(navigator.userAgent) && !/\sQQ/i.test(navigator.userAgent);
    if(navigator.userAgent.match(/iPad|iPhone|iPod|Baidu|UCBrowser/i) || isQQBrowser) {
        var type='normal'
    }else if(url.indexOf(".m3u8")>0){
            var _peerId = '', _peerNum = 0, _totalP2PDownloaded = 0, _totalP2PUploaded = 0;  
            var type='customHls'
        }else{var type='normal'}
        
    const dp = new DPlayer({
        container: document.getElementById('dplayer'),
        autoplay: true,
        hotkey: true,  // 移动端全屏时向右划动快进，向左划动快退。
        video: {
            url: url,
            type: type,
            customType: {
                'customHls': function (video, player) {
                    const hls = new Hls({
                        debug: false,
                        // Other hlsjsConfig options provided by hls.js
                        p2pConfig: {
                            live: false,        // 如果是直播设为true
                            // Other p2pConfig options provided by CDNBye
                        }
                    });
                    hls.loadSource(video.src);
                    hls.attachMedia(video);
                    hls.p2pEngine.on('stats', function (stats) {
                        _totalP2PDownloaded = stats.totalP2PDownloaded;
                        _totalP2PUploaded = stats.totalP2PUploaded;
                        updateStats();
                    }).on('peerId', function (peerId) {
                        _peerId = peerId;
                    }).on('peers', function (peers) {
                        _peerNum = peers.length;
                        updateStats();
                    });
                }
            }
        }
    });

    function updateStats() {
        var text = 'P2P正在为您加速' + (_totalP2PDownloaded/1024).toFixed(2)
            + 'MB 已分享' + (_totalP2PUploaded/1024).toFixed(2) + 'MB' + ' 连接节点' + _peerNum + '个';
        document.getElementById('stats').innerText = text
    }
</script>

</body>
</html>