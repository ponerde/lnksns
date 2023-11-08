<script>
	const api = require('@/config/api');
	const util = require('@/utils/util');
	export default {
		onLaunch: function() {
			//获取设备信息
			let that = this;
			uni.getSystemInfo({
				success: function success(res) {
					if (res.platform == 'ios' || res.platform == 'android') {
						that.globalData.isPc = false;
					} else {
						that.globalData.isPc = true;
					}
					that.globalData.windowHeight = res.windowHeight;
					that.globalData.screenHeight = res.screenHeight;
					that.globalData.statusBarHeight = res.statusBarHeight;
				}
			})
			const updateManager = uni.getUpdateManager();
			updateManager.onCheckForUpdate(function(res) {
				console.log(res.hasUpdate);
			});
			updateManager.onUpdateReady(function(res) {
				updateManager.applyUpdate();
			});
		},
		onShow: function() {
			let that = this;
			util.loginNow().then(function(res) {
				that.$isResolve();
				that.unreadMessage();
			});
			util.request(api.configUrl).then(function(res) {
				uni.setStorageSync('config', res.data);
			})
		},
		methods: {
			unreadMessage() {
				util.request(api.messageCountUrl).then(function(res) {
					if (res.data) {
						uni.setTabBarBadge({
							index: 2,
							text: res.data.toString()
						})
					} else {
						uni.removeTabBarBadge({
							index: 2,
						})
					}
				})
			}
		},
		onHide: function() {},
		globalData: {
			dw: 0,
			isPc: false,
			windowHeight: 0,
			screenHeight: 0,
			statusBarHeight: 0,
			shareTitle: api.AppTitle,
		}
	}
</script>

<style lang="scss">
	/* display: flex */
	.df {
		display: flex;
		align-items: center;
	}

	/* 字符串过长省略 */
	.ohto {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.ohto2 {
		display: block;
		white-space: pre-line;
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 2;
		text-overflow: ellipsis;
		-webkit-box-orient: vertical;
	}

	/* empty-box */
	.empty-box {
		padding: 150rpx 0;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.empty-box image {
		padding-bottom: 24rpx;
		width: 450rpx;
		height: 450rpx;
		// animation: fadeIn .6s ease;
	}

	.empty-box .text {
		color: #999;
		font-size: 24rpx;
		font-weight: 500;
	}

	/* button */
	button::after {
		border-radius: 0;
		border: none;
	}

	/* scroll */
	::-webkit-scrollbar {
		display: none;
		width: 0;
		height: 0;
		color: transparent;
	}

	/* 图片延迟加载 */
	@keyframes fadeIn {
		0% {
			opacity: 0;
		}

		50% {
			opacity: 0.5;
		}

		100% {
			opacity: 1;
		}
	}

	/* 没有更多了 */
	.no-more {
		width: 100%;
		height: 60px;
		line-height: 60px;
		text-align: center;
		color: #999;
		font-size: 20rpx;
	}

	/* 自定义提示 */
	.tips-box {
		width: 100%;
		justify-content: center;
	}

	.tips-box .tips-item {
		padding: 0 50rpx;
		height: 100rpx;
		line-height: 100rpx;
		font-size: 24rpx;
		font-weight: bold;
		color: #fff;
		background: rgba(0, 0, 0, .85);
		border-radius: 50rpx;
	}

	/* 默认背景图片 */
	.img-bg {
		background: #ededed url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyVpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ4IDc5LjE2NDAzNiwgMjAxOS8wOC8xMy0wMTowNjo1NyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIDIxLjAgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzE2OEQ4Rjk1ODJEMTFFREJGMThFMTEyQjIyNkZGRjEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzE2OEQ4RkE1ODJEMTFFREJGMThFMTEyQjIyNkZGRjEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDMTY4RDhGNzU4MkQxMUVEQkYxOEUxMTJCMjI2RkZGMSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDMTY4RDhGODU4MkQxMUVEQkYxOEUxMTJCMjI2RkZGMSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Prh7HHEAAAAkUExURe3t7ebm5u7u7ufn5+jo6Ovr6+np6ezs7Orq6uXl5enq6e7t7hwK1zIAAAIQSURBVHja7JjLbuwgEEShuw3cm///39A8bKQBeyQKKwtqEWW84LhfBdiYra2tra2trb8m/wKDDjHUe+4dMhLbo5BnQcbCHQoFax0BIWIte12Q6h/SZzYAIaQLKsUHJyIu+Jiq+MgeSIjTFTkoK0sSwyLbTtPfE2N7+AVI7OGuHHYeuQ+hN9LlaXkg0FBSIJ8chvawjgnHQZSGIMHrPAKnMa4maiaRo6/PceTVyhiarwRJMRnj/gUiqo/dDxLCl42dbw+PpBhu6oHcuMnQcDacTDjWOOrgPB4xYQHrkMUfxTk5eyv/K+aFYYTuJ8Gu967YuD0GNFnn3riWob3Lg2RFI/AGlDf6TFmel6B+AOqAfAb6CKVuNaAe6Hi9b4qFoPQqr6ZyPUdkjPunlWuEBNBcw4kXWCjdbMW3J3NRZBEkuzC3PxZAdD8ph+/5zWUEkdY/ZVEkZeoFkq8hJM1g7b3JfJHY24O9IKZ+CMkpKlWZvUoMITlF/jSzJRBpXWdy6IcQbhsjLILkty9Fmaw829uilCYWswgiza2SFzh9s/ABgLgxJBWFAJCbbOnKVE8ZM5DRMbVS/teb3kThzw3jUTNzIl8yeFHVQTeJh4JgGP5LhEx8A/kqEGY3dbJ/rggHvTvQsmGHnIQebAv3+Y7upwTzzatcsdiFoyfUTc4cIcTF+gJeS7EX9q2tra2tra1fAQYAMfoMyFH8Gw4AAAAASUVORK5CYII=) no-repeat center center;
	}

	/* 摇晃动画 */
	.animate {
		animation: wobble 1.5s .15s linear infinite;
		animation: wobble 1.5s .15s linear infinite;
		animation: wobble 1.5s .15s linear infinite;
		animation: wobble 1.5s .15s linear infinite;
	}

	@-webkit-keyframes wobble {
		10% {
			transform: rotate(15deg);
		}

		20% {
			transform: rotate(-10deg);
		}

		30% {
			transform: rotate(5deg);
		}

		40% {
			transform: rotate(-5deg);
		}

		50%,
		100% {
			transform: rotate(0deg);
		}
	}

	/* 颜色变量 */
	$uni-color: #000000;
	$uni-color-red: #FA5150;
	$uni-color-green: #4cd964;
</style>