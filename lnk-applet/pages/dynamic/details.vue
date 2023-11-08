<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box" :style="{paddingTop: statusBarHeight+'px'}">
			<view class="nav-item df">
				<view class="nav-back df" @click="navBack">
					<image src="/static/img/back.png"></image>
				</view>
				<view class="nav-user df" :data-url="'user/details?id='+info.user_id" @click="navigateToFun">
					<image class="nav-user-avatar" :src="info.user.avatar"></image>
					<view class="nav-user-info">
						<view class="name ohto">{{ info.user.name }}</view>
						<view class="tag" v-if="info.user.career">@{{ info.user.career }}</view>
					</view>
				</view>
			</view>
		</view>
		<!-- Swiper Box -->
		<view :style="{marginTop: statusBarHeight+48+'px'}">
			<block v-if="info.img.length">
				<swiper class="swiper-box"
					:style="{height:info.img[swiperIdx].wide!=info.img[swiperIdx].high?(750*info.img[swiperIdx].high)/info.img[swiperIdx].wide +'rpx':'750rpx'}"
					circular @change="swiperChange">
					<swiper-item class="swiper-item" v-for="(item,idx) in info.img" :key="idx" :data-i="idx"
						@click="swiperClick">
						<lazy-load :src="item.url"></lazy-load>
					</swiper-item>
				</swiper>
				<view class="indicator df">
					<view v-if="info.img.length>1" class="indicator-item"
						:style="{width:'calc(100% / '+info.img.length+' - 10rpx)',background:i<=swiperIdx?'rgba(255, 255, 255, .85)':'#ffffff60'}"
						v-for="(v,i) in info.img.length" :key="i">
					</view>
				</view>
			</block>
			<!-- Info Box -->
			<view class="info-box">
				<view class="cot">{{ info.content }}</view>
				<view class="topic-box df">
					<view class="topic-item df" :data-url="'circle/details?id='+info.circle_id" @click="navigateToFun">
						<image src="/static/img/qz.png"></image>
						<view class="name">{{ info.circle_name }}</view>
					</view>
					<view class="topic-item df" v-if="info.adds" @click="openLocationClick">
						<image src="/static/img/dw.png"></image>
						<view class="name ohto">{{ info.adds }}</view>
					</view>
				</view>
				<view class="more df">
					<view class="more-left">
						<text>{{ info.create_time_text }}</text>
						<text>{{ info.province }}</text>
					</view>
					<image v-if="info.user_id == user_id" class="more-right" @click="openMorePopup"
						src="/static/img/gd.png"></image>
				</view>
			</view>
			<!-- Comment Box -->
			<view class="comment-box">
				<view class="comment-title">评论 {{info.comment_count?info.comment_count:''}}</view>
				<view v-if="isEmpty" class="empty-box">
					<image :src="insetUrl+'7.png'" />
					<view class="text df">
						沙发还空着，<view @click="openComment" data-type="0" style="color: #4cd964;" :data-idx="0"
							:data-i="-1">
							立即抢沙发</view>
					</view>
				</view>
				<view v-else class="comment-item df" v-for="(item,idx) in comment_arr" :key="idx">
					<view class="comment-img1" :data-url="'user/details?id='+item.user.id" @click="navigateToFun">
						<lazy-load :src="item.user.avatar" border-radius="68rpx"></lazy-load>
					</view>
					<view class="comment-info">
						<view class="comment-info-top df" :data-url="'user/details?id='+item.user.id"
							@click="navigateToFun">
							<view class="zz" v-if="info.user_id == item.user.id">作者</view>{{item.user.name}}
						</view>
						<view class="comment-info-content" :class="{'del-cor':item.status==0}" @click="openComment"
							data-type="1" :data-uid="item.user.id" :data-cid="item.id" :data-name="item.user.name"
							:data-idx="idx" :data-i="0">
							{{item.content}}
						</view>
						<view class="comment-info-bottom df">
							<view>{{item.create_time_str}} {{item.province}}</view>
							<view @click="openComment" data-type="1" :data-uid="item.user.id" :data-cid="item.id"
								:data-name="item.user.name" :data-idx="idx" :data-i="0">回复</view>
							<view v-if="user_id==item.user.id && item.status==1" @click="delComment" :data-id="item.id"
								:data-idx="idx">删除</view>
						</view>
						<!-- 回复 -->
						<view v-if="item.list_count" class="comment-item df" v-for="(v,i) in item.list" :key="i">
							<view class="comment-img2" :data-url="'user/details?id='+v.user.id" @click="navigateToFun">
								<lazy-load :src="v.user.avatar" border-radius="48rpx"></lazy-load>
							</view>
							<view class="comment-info" style="width: calc(100% - 68rpx)">
								<view class="comment-info-top2 df">
									<view class="zz" v-if="info.user_id == v.user.id">作者</view>
									<view class="nn ohto" :data-url="'user/details?id='+v.user.id"
										@click="navigateToFun">{{v.user.name}}
									</view>
									<text v-if="v.reply_user.id != item.user.id">回复</text>
									<view class="nn ohto" :data-url="'user/details?id='+v.reply_user.id"
										@click="navigateToFun" v-if="v.reply_user.id != item.user.id">
										{{v.reply_user.name}}
									</view>
								</view>
								<view class="comment-info-content" :class="{'del-cor':v.status==0}" @click="openComment"
									data-type="1" :data-uid="v.user.id" :data-cid="item.id" :data-name="v.user.name"
									:data-idx="idx" :data-i="i">
									{{v.content}}
								</view>
								<view class="comment-info-bottom df">
									<view>{{v.create_time_str}} {{v.province}}</view>
									<view @click="openComment" data-type="1" :data-uid="v.user.id" :data-cid="item.id"
										:data-name="v.user.name" :data-idx="idx" :data-i="i">回复</view>
									<view v-if="user_id==v.user.id && v.status==1" @click="delComment" :data-id="v.id"
										:data-idx="idx" :data-i="i">删除</view>
								</view>
							</view>
						</view>
						<block v-if="item.list_count">
							<view @click="sonComment" :data-id="item.id" :data-idx="idx" class="unfold"
								v-if="item.list_count > item.list.length">
								{{son_page==1?'展开 '+(item.list_count-4)+' 条回复':'展开更多回复'}}
							</view>
						</block>
					</view>
				</view>
				<block v-if="1==1">
					<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
					<view v-else class="no-more">没有更多了</view>
				</block>
			</view>
		</view>
		<!-- Footer Box -->
		<view class="footer-box">
			<view class="footer-item df">
				<view class="footer-comment df" @click="openComment" data-type="0" data-idx="0" data-i="-1">
					<view class="ohto">{{comtext?comtext:comtips}}</view>
				</view>
				<view class="df">
					<button class="footer-icon df">
						<image src="/static/img/pl.png"></image>
						<text>{{ info.comment_count?info.comment_count:'评论' }}</text>
					</button>
					<button class="footer-icon df" @click="likeDynamic">
						<image v-if="info.is_like" src="/static/img/xhs.png"></image>
						<image v-else src="/static/img/xh.png"></image>
						<text>{{ info.like_count?info.like_count:'赞' }}</text>
					</button>
					<button class="footer-icon df" open-type="share">
						<image src="/static/img/fx.png"></image>
					</button>
				</view>
			</view>
		</view>
		<!-- Popup Comment -->
		<view v-if="isComment" class="popup-comment-mask" @click="closeComment"></view>
		<view v-if="isComment" class="popup-comment">
			<view class="comment-top">
				<view class="comment-textarea">
					<textarea v-model="comtext" @focus="openCommentTextarea" :focus="isFocus" :placeholder="comtips"
						cursor-spacing="120" :show-confirm-bar="false"></textarea>
				</view>
				<image v-if="comtext.length" @click="confirmFun" class="send" src="/static/img/fss.png"></image>
				<image v-else class="send" src="/static/img/fsh.png"></image>
			</view>
		</view>
		<!-- popup -->
		<uni-popup ref="morePopup" type="bottom" :safe-area="false">
			<view class="more-popup">
				<view v-if="info.user_id == user_id" :data-url="'dynamic/add?id='+info.id" @click="navigateToFun">编辑
				</view>
				<view v-if="info.user_id == user_id" @click="delDynamic" style="color: #FA5150;">删除</view>
				<view style="color: #999;" @click="closeMorePopup">取消</view>
			</view>
		</uni-popup>
		<!-- Tips Box -->
		<uni-popup ref="tipsPopup" type="top" mask-background-color="rgba(0, 0, 0, 0)">
			<view class="tips-box df" :style="{marginTop: statusBarHeight+44+'px'}">
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
				insetUrl: api.insetUrl,
				user_id: 0,
				user_avatar: '',
				id: 0,
				info: {
					user: {
						name: '...'
					},
					content: '...',
					img: [],
				},
				swiperIdx: 0,
				isEmpty: false,
				comment_arr: [],
				c_idx: 0,
				c_i: 0,
				page: 1,
				son_page: 1,
				loadStatus: 'more',
				c_c_id: 0,
				c_u_id: 0,
				isComment: false,
				isFocus: false,
				comtips: '说点什么..',
				comtext: '',
				tips_title: '',
			}
		},
		async onLoad(option) {
			uni.showShareMenu();
			await this.$onLaunched;
			let that = this;
			if (option.id) {
				that.id = option.id;
				that.dynamicDetails();
				that.dynamicComment();
			} else {
				that.tips_title = '未找到动态或动态异常！';
				that.$refs.tipsPopup.open();
				setTimeout(function() {
					that.$refs.tipsPopup.close();
					that.navBack();
				}, 2000);
			}
		},
		onShow() {
			let user = uni.getStorageSync('user_info');
			this.user_avatar = user.avatar;
			this.user_id = user.id;
		},
		methods: {
			dynamicDetails() {
				let that = this;
				util.request(api.dynamicDetailsUrl, {
					id: that.id
				}).then(function(res) {
					let is_pop = false;
					if (res.code == 200) {
						if (res.data.status != 1 && that.user_id != res.data.user_id) {
							is_pop = true;
							that.tips_title = '未找到动态或动态异常！';
						} else {
							that.info = res.data;
						}
					} else {
						is_pop = true;
						that.tips_title = res.msg;
					}
					if (is_pop) {
						that.$refs.tipsPopup.open();
						setTimeout(function() {
							that.$refs.tipsPopup.close();
							that.navBack();
						}, 2000);
					}
				});
			},
			dynamicComment() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.dynamicCommentUrl, {
					id: that.id,
					page: that.page,
				}).then(function(res) {
					if (res.data.data.length > 0) {
						that.comment_arr = that.comment_arr.concat(res.data.data);
						that.page = res.data.current_page;
						that.loadStatus = 'more';
					} else if (that.page == 1) {
						that.loadStatus = 'more';
						that.isEmpty = true;
					} else {
						that.loadStatus = 'no-more';
					}
				})
			},
			sonComment(e) {
				let that = this;
				let id = e.currentTarget.dataset.id;
				let idx = e.currentTarget.dataset.idx;
				that.son_page = that.son_page + 1;
				util.request(api.sonCommentUrl, {
					id: id,
					page: that.son_page,
				}).then(function(res) {
					let data = res.data;
					that.comment_arr[idx].list = that.comment_arr[idx].list.concat(data.data);
					that.son_page = data.current_page;
				})
			},
			openComment(e) {
				let data = e.currentTarget.dataset;
				let type = data.type;
				let uid = data.uid ? data.uid : 0;
				let cid = data.cid ? data.cid : 0;
				let name = data.name ? data.name : '';
				this.c_idx = data.idx ? data.idx : 0;
				this.c_i = data.i != -1 ? data.i : -1;
				this.isComment = true;
				this.isFocus = true;
				if (type == 1) {
					this.c_c_id = cid;
					this.c_u_id = uid;
					this.comtips = '回复：' + name;
				}
			},
			confirmFun() {
				let that = this;
				if (!that.comtext) {
					that.tips_title = '你得说点什么才能评论呀';
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
					return;
				}
				util.request(api.commentDynamicUrl, {
					id: that.info.id,
					duid: that.info.user_id,
					ct: that.comtext,
					img: that.info.img.length > 0 ? that.info.img[0].url : '',
					reply_comment_id: that.c_c_id,
					reply_user_id: that.c_u_id,
				}, 'POST').then(function(res) {
					that.tips_title = res.msg;
					if (res.code == 200) {
						if (that.c_idx == 0 && that.c_i == '-1') {
							if (that.comment_arr.length <= 0) {
								that.isEmpty = false;
								that.comment_arr = [];
							}
							that.comment_arr.unshift(res.data);
						} else if (that.c_idx >= 0 && that.c_i == 0) {
							if (that.comment_arr[that.c_idx].list_count <= 0) that.comment_arr[that.c_idx]
								.list = [];
							that.comment_arr[that.c_idx].list.unshift(res.data);
							that.comment_arr[that.c_idx].list_count = that.comment_arr[that.c_idx].list_count + 1;
						} else if (that.c_idx >= 0 && that.c_i > 0) {
							if (that.comment_arr[that.c_idx].list_count <= 0) that.comment_arr[that.c_idx]
								.list = [];
							that.comment_arr[that.c_idx].list.push(res.data);
							that.comment_arr[that.c_idx].list_count = that.comment_arr[that.c_idx].list_count + 1;
						}
						that.comtext = '';
						that.c_c_id = 0;
						that.c_u_id = 0;
						that.comtips = '说点什么..';
					}
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
				});
			},
			delComment(e) {
				let that = this;
				let data = e.currentTarget.dataset;
				let idx = data.idx;
				let i = data.i ? data.i : -1;
				uni.showModal({
					title: '确定要删除该评论吗？',
					success: function(res) {
						if (res.confirm) {
							that.tips_title = '操作成功，已删除评论';
							that.$refs.tipsPopup.open();
							setTimeout(function() {
								that.$refs.tipsPopup.close();
							}, 1500);
							if (i == -1) {
								that.comment_arr[idx].content = '（该评论已被删除）';
								that.comment_arr[idx].status = 3;
							} else {
								that.comment_arr[idx].list[i].content = '（该评论已被删除）';
								that.comment_arr[idx].list[i].status = 3;
							}
							util.request(api.delCommentUrl, {
								id: data.id,
							}, 'POST');
						}
					}
				});
			},
			likeDynamic() {
				let that = this;
				that.info.is_like = !that.info.is_like;
				if (that.info.is_like) that.info.like_count++;
				if (!that.info.is_like) that.info.like_count--;
				util.request(api.likeDynamicUrl, {
					id: that.info.id,
					duid: that.info.user_id,
					is_like: that.info.is_like,
					content: that.info.content,
					img: that.info.img[0].url ? that.info.img[0].url : '',
				}, 'POST');
			},
			delDynamic() {
				let that = this;
				uni.showModal({
					content: '确认要删除这篇动态吗？',
					success: function(res) {
						if (res.confirm) {
							util.request(api.delDynamicUrl, {
								id: that.info.id
							}, "POST").then(function(res) {
								app.globalData.dw = 1;
								that.tips_title = '已为您删除这篇动态';
								that.$refs.tipsPopup.open();
								setTimeout(function() {
									that.$refs.tipsPopup.close();
									that.navBack();
								}, 1500);
							});
						}
					}
				});
			},
			swiperClick(e) {
				let idx = e.currentTarget.dataset.i;
				let urls = [];
				for (let v of this.info.img) {
					urls.push(v.url);
				}
				uni.previewImage({
					current: idx,
					urls: urls,
				});
			},
			closeComment() {
				if (this.isComment) {
					this.isComment = false;
					this.isFocus = false;
					this.c_idx = 0;
					this.c_i = -1;
					this.c_c_id = 0;
					this.c_u_id = 0;
					this.comtips = '说点什么..';
				}
			},
			openCommentTextarea() {
				this.isFocus = true;
			},
			swiperChange(e) {
				this.swiperIdx = e.detail.current;
			},
			openLocationClick(e) {
				uni.openLocation({
					latitude: parseFloat(this.info.lat),
					longitude: parseFloat(this.info.lng),
					name: this.info.adds
				});
			},
			openMorePopup() {
				this.$refs.morePopup.open();
			},
			closeMorePopup() {
				this.$refs.morePopup.close();
			},
			navigateToFun(e) {
				let url = e.currentTarget.dataset.url;
				uni.navigateTo({
					url: '/pages/' + url
				})
			},
			navBack() {
				let currentRoutes = getCurrentPages();
				if (currentRoutes.length > 1) {
					uni.navigateBack();
				} else {
					uni.switchTab({
						url: '/pages/tabbar/index'
					})
				}
			},
			onReachBottom() {
				if (!this.isEmpty && this.comment_arr.length) {
					this.page = this.page + 1;
					this.dynamicComment();
				}
			},
		},
		onShareAppMessage: function(e) {
			return {
				title: this.info.content,
				imageUrl: this.info.img.length > 0 ? this.info.img[0].url : '',
				path: '/pages/dynamic/details?id=' + this.id
			}
		},
		onShareTimeline(res) {
			return {
				title: app.globalData.shareTitle
			}
		},
	}
</script>

<style>
	@import url("/static/css/dynamic/details.css");
</style>