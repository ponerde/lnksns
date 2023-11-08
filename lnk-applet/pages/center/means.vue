<template>
	<view class="container">
		<view class="navbar" :style="{paddingTop: statusBarHeight+'px'}">
			<view class="navbar-item">
				<view class="back-box df" @click="navBack">
					<image src="/static/img/back.png"></image>
				</view>
			</view>
		</view>
		<view class="title-box" :style="{marginTop: statusBarHeight+44+'px'}">
			<view>完善你的资料📸</view>
			<view>让大家更好地了解你</view>
		</view>
		<button class="avatar-box df" open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
			<image class="avatar-item" :src="avatar" mode="aspectFill"></image>
			<image class="camera-icon" src="/static/img/pz.png"></image>
		</button>
		<view class="title-label">昵称</view>
		<input class="input-box" @blur="nameBlur" v-model="name" cursor-spacing="10" type="nickname" maxlength="16"
			placeholder="不能超过16个字哦" />
		<view class="df sp">
			<view class="title-label w50">性别</view>
			<view class="title-label w50">年龄</view>
		</view>
		<view class="df sp">
			<view class="gender-box df w50 sp">
				<view @click="genderClick(0)" class="gender-item df" :class="{'active-1':gender==0}">
					<image src="/static/img/nv.png"></image>
				</view>
				<view @click="genderClick(1)" class="gender-item df" :class="{'active-2':gender==1}">
					<image src="/static/img/nan.png"></image>
				</view>
			</view>
			<view class="input-box df age" @click="openAge">
				<view :style="{color: age ? '#000':'#999'}">
					{{age ? age:'选择你的年龄'}}
				</view>
				<image class="pup" src="/static/img/back.png"></image>
			</view>
		</view>
		<view class="title-label">职业</view>
		<view class="input-box df" @click="openCareer">
			<view :style="{color: career ? '#000':'#999'}">
				{{career ? career:'选择你的职业'}}
			</view>
			<image class="pup" src="/static/img/back.png"></image>
		</view>
		<view class="title-label">手机号</view>
		<view class="input-box df">
			<view>{{ mobile ? mobile:'暂未绑定' }}</view>
			<button class="ip-btn df" open-type="getPhoneNumber" @getphonenumber="bindMobileClick">
				<image src="/static/img/hb.png"></image>
				<text>{{mobile?'换绑':'绑定'}}</text>
			</button>
		</view>
		<view class="title-label df" @click="ipClick">
			<text>IP属地</text>
			<image src="/static/img/ts.png"></image>
		</view>
		<view class="input-box df">
			<view>{{ province }}</view>
			<view class="ip-btn df" @click="ipRefreshClick">
				<image src="/static/img/sx.png"></image>
				<text>刷新</text>
			</view>
		</view>
		<!-- Popover Box -->
		<uni-popup ref="agePopup" type="bottom" :safe-area="false">
			<view class="age-popup">
				<view class="age-box">
					<block v-for="(v,i) in agearr" :key="i">
						<view class="age-item" @click="age=v" :data-value="v" :class="{'active':age==v}">
							{{v}}
						</view>
					</block>
				</view>
				<view class="age-btn df">
					<view class="btn1" @click="closeAge(0)">暂不选择</view>
					<view class="btn2" @click="closeAge(1)">确定</view>
				</view>
			</view>
		</uni-popup>
		<uni-popup ref="careerPopup" type="bottom" :safe-area="false">
			<view class="age-popup">
				<view class="age-box">
					<block v-for="(v,i) in careerarr" :key="i">
						<view class="age-item" @click="career=v" :data-value="v" :class="{'active':career==v}">
							{{v}}
						</view>
					</block>
				</view>
				<view class="age-btn df">
					<view class="btn1" @click="closeCareer(0)">暂不选择</view>
					<view class="btn2" @click="closeCareer(1)">确定</view>
				</view>
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
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				name: '...',
				avatar: '',
				gender: 0,
				career: '',
				age: '',
				mobile: '',
				province: '',
				tips_title: '',
				careerarr: uni.getStorageSync('config').lnk_zy,
				agearr: uni.getStorageSync('config').lnk_nl,
			}
		},
		onLoad() {
			let user_info = uni.getStorageSync('user_info');
			this.name = user_info.name;
			this.avatar = user_info.avatar;
			this.gender = user_info.gender;
			this.career = user_info.career;
			this.age = user_info.age;
			this.mobile = user_info.mobile;
			this.province = user_info.province;
		},
		methods: {
			nameBlur() {
				if (this.name != uni.getStorageSync('user_info').name) {
					this.userUpInfo();
				}
			},
			userUpInfo() {
				let that = this;
				let user_info = uni.getStorageSync('user_info');
				if (user_info.name != that.name || user_info.avatar != that.avatar || user_info.gender != that.gender ||
					user_info.career != that.career || user_info.age != that.age) {
					util.request(api.editUserInfoUrl, {
						avatar: that.avatar,
						name: that.name,
						gender: that.gender,
						career: that.career,
						age: that.age,
					}, 'post').then(function(res) {
						user_info.name = that.name;
						user_info.avatar = that.avatar;
						user_info.gender = that.gender;
						user_info.career = that.career;
						user_info.age = that.age;
						uni.setStorageSync('user_info', user_info);
						that.tips_title = res.msg;
						that.$refs.tipsPopup.open();
						setTimeout(function() {
							that.$refs.tipsPopup.close();
						}, 1500);
					});
				}
			},
			genderClick(gender) {
				if (this.gender != gender) {
					this.gender = gender;
					this.userUpInfo();
				}
			},
			openCareer() {
				this.$refs.careerPopup.open();
			},
			closeCareer(type) {
				if (type == 1) this.userUpInfo();
				this.$refs.careerPopup.close();
			},
			openAge() {
				this.$refs.agePopup.open();
			},
			closeAge(type) {
				if (type == 1) this.userUpInfo();
				this.$refs.agePopup.close();
			},
			onChooseAvatar(e) {
				let that = this;
				var filePath = e.detail.avatarUrl;
				if (filePath) {
					uni.showLoading({
						title: '正在上传..',
						mask: true
					});
					util.lnkUploadFile(filePath).then(function(res) {
						that.avatar = res.data.url;
						that.userUpInfo();
						uni.hideLoading();
					})
				}
			},
			bindMobileClick(e) {
				let that = this;
				uni.showLoading({
					title: '换绑中..',
					mask: true
				});
				util.request(api.userBindMobileUrl, {
					code: e.detail.code,
				}, 'POST').then(function(res) {
					uni.hideLoading();
					that.tips_title = res.msg;
					if (res.code == 200) {
						uni.setStorageSync('user_info', res.data);
						that.mobile = res.data.mobile;
						app.globalData.isUserInfo = true;
					}
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
				});
			},
			ipRefreshClick() {
				let that = this;
				uni.showLoading({
					title: '刷新中..',
					mask: true
				});
				util.request(api.userRefreshIpUrl, {}, 'POST').then(function(res) {
					uni.hideLoading();
					if (res.code == 200) {
						that.tips_title = '刷新成功 🎉';
						uni.setStorageSync('user_info', res.data);
						that.province = res.data.province;
						app.globalData.isUserInfo = true;
					} else {
						that.tips_title = res.msg;
					}
					that.$refs.tipsPopup.open();
					setTimeout(function() {
						that.$refs.tipsPopup.close();
					}, 1500);
				});
			},
			ipClick() {
				uni.showModal({
					title: 'IP属地说明',
					content: '为维护网络安全、保障良好生态和社区的真实性，根据网络运营商数据，展示用户IP属地信息。',
					showCancel: false,
					confirmText: '我知道了',
					confirmColor: '#000000'
				});
			},
			navBack() {
				let currentRoutes = getCurrentPages();
				if (currentRoutes.length > 1) {
					uni.navigateBack();
				} else {
					uni.switchTab({
						url: '/pages/tabbar/center'
					})
				}
			},
		},
	}
</script>

<style>
	@import url("/static/css/center/means.css");
</style>