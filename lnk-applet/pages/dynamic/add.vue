<template>
	<view class="container df">
		<view class="textarea-box">
			<textarea v-model="ct" maxlength="2000" auto-height="true" placeholder="分享灵感和作品"></textarea>
		</view>
		<scroll-view scroll-x="true" class="scroll">
			<view class="scroll_box">
				<view class="img-box" v-for="(item,idx) in pic_list" :key="id">
					<image class="pic" :src="item.url" mode="aspectFill"></image>
					<view class="del df" @click="delImg(idx)">＋</view>
				</view>
				<view @click="addImg" class="add df">＋</view>
				<view style="flex-shrink: 0;width: 30rpx;"></view>
			</view>
		</scroll-view>
		<view class="topic-box df">
			<view class="topic-item df" @click="openCircle">
				<image src="/static/img/qz.png"></image>
				<view class="name">{{circle?circle:'选择圈子'}}</view>
			</view>
			<view class="topic-item df" @click="locationClick">
				<image src="/static/img/dw.png"></image>
				<view class="name ohto">{{adds.name?adds.name:'添加位置'}}</view>
			</view>
		</view>
		<view class="footer">
			<view class="footer_box df">
				<view class="tips df" @click="openTipsPopup">
					<view class="tips-item df">
						<image src="/static/img/ts.png"></image>
					</view>
					<text>小贴士</text>
				</view>
				<view class="btn df" @click="saveDynamic">
					<text>发布动态</text>
				</view>
			</view>
		</view>
		<!-- Tips Popup -->
		<uni-popup ref="circlePopup" type="bottom" :safe-area="false">
			<view class="circle-popup">
				<view class="circle-box">
					<block v-for="(v,i) in circlearr" :key="i">
						<view class="circle-item df" @click="circleClick" :data-i="i"
							:class="{'active':circle_id==v.id}">
							<image :src="v.avatar" mode="aspectFill"></image>
							<text>{{ v.name }}</text>
						</view>
					</block>
				</view>
				<view class="circle-btn df">
					<view class="btn1" @click="closeCircle">暂不选择</view>
					<view class="btn2" @click="closeCircle">确定</view>
				</view>
			</view>
		</uni-popup>
		<uni-popup ref="addPopup" type="center">
			<view class="tips-popup df">
				<view class="tips-popup-t1">发布小贴士</view>
				<view class="tips-popup-t2">我们鼓励向上、真实、原创的内容，含以下内容的动态将不会被推荐：</view>
				<view class="tips-popup-t2">1. 含有不文明语言、过度性感图片；</view>
				<view class="tips-popup-t2">2. 含有投资股票、基金、涉政等信息；</view>
				<view class="tips-popup-t2">3. 冒充他人身份或搬运他人作品；</view>
				<view class="tips-popup-t2">4. 通过有奖方式诱导他人收藏、评论、转发、关注；</view>
				<view class="tips-popup-t2">5. 为刻意搏眼球，在内容、图片等处使用夸张表达。</view>
				<view class="tips-popup-btn df" @click="closeTipsPopup"> 我知道了</view>
			</view>
		</uni-popup>
		<uni-popup ref="tipsPopup" type="top" mask-background-color="rgba(0, 0, 0, 0)">
			<view class="tips-box df">
				<view class="tips-item">{{tips_title}}</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	const app = getApp();
	const api = require('@/config/api');
	const util = require('@/utils/util');
	export default {
		components: {},
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				id: 0,
				ct: '',
				circle: '',
				circle_id: 0,
				circlearr: [],
				adds: {},
				pic_list: [],
				tips_title: '',
			}
		},
		onLoad(options) {
			if (options.id && options.id > 0) {
				this.id = options.id;
				this.getDynamicInfo();
			}
			this.dynamicCircle();
		},
		methods: {
			getDynamicInfo() {
				let that = this;
				util.request(api.getDynamicInfoUrl, {
					id: that.id
				}).then(function(res) {
					that.id = res.data.id;
					that.ct = res.data.content;
					that.pic_list = res.data.imgs;
					that.circle_id = res.data.circle_id;
					that.circle = res.data.circle_name;
					that.adds.name = res.data.adds;
					that.adds.latitude = res.data.lat;
					that.adds.longitude = res.data.lng;
				});
			},
			dynamicCircle() {
				let that = this;
				util.request(api.dynamicCircleUrl).then(function(res) {
					that.circlearr = res.data;
				});
			},
			saveDynamic() {
				let that = this;
				if (!that.ct) {
					that.tips_title = '动态内容不能为空哦';
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
					return;
				} else if (!that.circle || !that.circle_id) {
					that.tips_title = '请选择一个圈子发布';
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
					return;
				}
				util.request(api.saveDynamicUrl, {
					id: that.id,
					content: that.ct,
					circle_id: that.circle_id,
					circle: that.circle,
					adds: that.adds,
					imgs: that.pic_list,
				}, 'POST').then(function(res) {
					that.tips_title = res.msg;
					if (res.code == 200) {
						setTimeout(function() {
							app.globalData.dw = 1;
							uni.switchTab({
								url: '/pages/tabbar/center'
							});
						}, 1000);
					}
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
				});
			},
			addImg() {
				let that = this;
				let count = 20 - that.pic_list.length;
				uni.chooseImage({
					count: count,
					success: (res) => {
						uni.showLoading({
							title: '上传中...'
						});
						var filePath = res.tempFilePaths;
						that.getImageInfo(filePath).then(function(imgArr) {
							for (let i in imgArr) {
								util.lnkUploadFile(imgArr[i].url).then(function(fileImg) {
									imgArr[i].url = fileImg.data.url;
									if (imgArr.length == parseInt(i) + 1) {
										that.pic_list = that.pic_list.concat(imgArr);
										uni.hideLoading();
									}
								});
							}
						});
					},
					fail: (err) => {
						console.log('chooseImage fail', err)
					}
				})
			},
			delImg(idx) {
				this.pic_list.splice(idx, 1);
			},
			getImageInfo(imgs) {
				return new Promise(function(resolve, reject) {
					let imgArr = [];
					for (let v of imgs) {
						uni.getImageInfo({
							src: v,
							success: function(res) {
								let item = {};
								item.url = res.path;
								item.wide = res.width;
								item.high = res.height;
								imgArr.push(item);
								if (imgs.length == imgArr.length) {
									resolve(imgArr);
								}
							}
						});
					}
				});
			},
			locationClick() {
				let that = this;
				uni.chooseLocation({
					success: function(res) {
						that.adds = res;
					}
				});
			},
			rescueClick(e) {
				let i = e.currentTarget.dataset.i;
				this.rescuearr[i].is_check = !this.rescuearr[i].is_check;
			},
			circleClick(e) {
				let i = e.currentTarget.dataset.i;
				this.circle_id = this.circlearr[i].id;
				this.circle = this.circlearr[i].name;
			},
			openCircle() {
				this.$refs.circlePopup.open();
			},
			closeCircle() {
				this.$refs.circlePopup.close();
			},
			openTipsPopup() {
				this.$refs.addPopup.open();
			},
			closeTipsPopup() {
				this.$refs.addPopup.close();
			},
		},
	}
</script>

<style>
	@import url("/static/css/dynamic/add.css");
</style>