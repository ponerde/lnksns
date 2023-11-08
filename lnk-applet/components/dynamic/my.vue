<template>
	<view>
		<view class="card-item" @click="toDynamic">
			<view class="card-item-avatar" @click.stop="toUser">
				<image :src="item.user.avatar" mode="aspectFill"></image>
			</view>
			<view class="card-item-info">
				<view class="item-info-user" @click.stop="toUser">
					<view class="info-user-name ohto">{{item.user.name}}</view>
					<view class="info-user-tag">{{item.create_time}}</view>
				</view>
				<view class="item-info-file" v-if="item.img_count">
					<block v-if="item.img_count == 1">
						<view class="info-wh"
							:class="{'info-h-w':item.img[0].wide < item.img[0].high,'info-w-h':item.img[0].wide > item.img[0].high}">
							<lazy-load :src="item.img[0].url" border-radius="8rpx"></lazy-load>
						</view>
					</block>
					<view v-else class="info-img-box" v-for="(img,i) in item.img" :key="i">
						<lazy-load :src="img.url" border-radius="8rpx"></lazy-load>
					</view>
					<view class="img-count df" v-if="item.img_count>2">
						<image src="/static/img/i.png"></image>
						<text>{{item.img_count}}</text>
					</view>
				</view>
				<view class="item-info-content ohto2">{{item.content}}</view>
				<view class="item-info-quantity df">
					<text>评论 {{item.comment_count}}</text>
					<text>赞 {{item.like_count}}</text>
					<text v-if="showView">浏览 {{item.view}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			item: {
				type: Object,
				require: true
			},
			idx: {
				type: Number,
				default: 0
			},
			showView: {
				type: Boolean,
				default: true
			},
		},
		data() {
			return {}
		},
		methods: {
			toDynamic() {
				uni.navigateTo({
					url: '/pages/dynamic/details?id=' + this.item.id
				})
			},
			toUser() {
				uni.navigateTo({
					url: '/pages/user/details?id=' + this.item.user_id
				})
			},
		}
	}
</script>

<style lang="scss" scoped>
	.card-item {
		width: 100%;
		background-color: #ffffff;
		display: flex;
		justify-content: space-between;
		padding-bottom: 40rpx;
	}

	.card-item .card-item-avatar {
		width: 68rpx;
		height: 68rpx;
		border-radius: 50%;
		overflow: hidden;
	}

	.card-item .card-item-avatar image {
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}

	.card-item .card-item-info {
		width: calc(100% - 88rpx);
	}

	.card-item-info .item-info-user {
		width: calc(100% - 120rpx);
	}

	.item-info-user .info-user-name {
		font-size: 26rpx;
		font-weight: bold;
	}

	.item-info-user .info-user-tag {
		color: #999;
		font-size: 18rpx;
		font-weight: 500;
	}

	.card-item-info .item-info-content {
		margin: 24rpx 0;
		color: #333;
		font-size: 28rpx;
		font-weight: 500;
		white-space: pre-line;
	}

	.card-item-info .item-info-file {
		margin-top: 24rpx;
		display: flex;
		position: relative;
	}

	.card-item-info .info-wh {
		width: 375rpx;
		height: 375rpx;
	}

	.card-item-info .info-h-w {
		width: 350rpx;
		height: 450rpx;
	}

	.card-item-info .info-w-h {
		width: 500rpx;
		height: 350rpx;
	}

	.item-info-file .info-img-box {
		width: 200rpx;
		height: 200rpx;
		margin-right: 8rpx;
	}

	.info-img-box:nth-child(3n) {
		margin-right: 0rpx !important;
	}

	.item-info-file .img-count {
		position: absolute;
		right: 20rpx;
		bottom: 40rpx;
		justify-content: center;
		width: 60rpx;
		height: 40rpx;
		color: #fff;
		font-size: 20rpx;
		font-weight: bold;
		background: rgba(0, 0, 0, .5);
		border-radius: 8rpx;
	}

	.item-info-file .img-count image {
		margin-right: 6rpx;
		width: 24rpx;
		height: 24rpx;
	}

	.card-item-info .item-info-quantity {
		width: 100%;
	}

	.card-item-info .item-info-quantity text {
		margin-right: 24rpx;
		color: #999;
		font-size: 20rpx;
	}
</style>