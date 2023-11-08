var api = require('../config/api.js');

function request(url, data = {}, method = "GET") {
	return new Promise(function(resolve, reject) {
		uni.request({
			url,
			data,
			method,
			header: {
				'Content-Type': 'application/json',
				'token': uni.getStorageSync('user_token')
			},
			success: function(res) {
				if (res.statusCode == 200) {
					if (res.data.error == 500002) {
						getNewToken().then(() => {
							request(url, data, method).then((res) => {
								resolve(res);
							})
						})
					} else {
						resolve(res.data);
					}
				} else {
					reject(res.errMsg);
				}
			},
			fail: function(err) {
				reject(err)
			}
		})
	});
}

function lnkUploadFile(filePath) {
	return new Promise(function(resolve, reject) {
		uni.uploadFile({
			url: api.uploadsUrl,
			filePath: filePath,
			name: 'file',
			header: {
				'token': uni.getStorageSync('user_token')
			},
			success: function(res) {
				let data = JSON.parse(res.data);
				if (res.statusCode == 200) {
					if (res.data.error == 500002) {
						getNewToken().then(() => {
							lnkUploadFile(filePath).then((res) => {
								resolve(res);
							})
						})
					} else {
						resolve(data);
					}
				} else {
					reject(res.errMsg);
				}
			},
			fail: function(err) {
				reject(err)
			}
		})
	});
}

function getNewToken() {
	return new Promise((resolve, reject) => {
		uni.login({
			provider: 'weixin',
			success: function(code) {
				request(api.wxEmpowerUrl, {
					code: code.code
				}, 'POST').then(function(res) {
					uni.setStorageSync('user_info', res.data.info);
					uni.setStorageSync('user_token', res.data.token);
					resolve(true);
				});
			},
		})
	});
}

function loginNow() {
	return new Promise((resolve, reject) => {
		let userInfo = uni.getStorageSync('user_info');
		if (userInfo == '' || userInfo == null || typeof(userInfo) == "undefined") {
			uni.login({
				provider: 'weixin',
				success: function(code) {
					request(api.wxEmpowerUrl, {
						code: code.code
					}, 'POST').then(function(res) {
						uni.setStorageSync('user_info', res.data.info);
						uni.setStorageSync('user_token', res.data.token);
						resolve(true);
					});
				},
			});
		} else {
			resolve(true);
		}
	});
}


/**
 * 
 * @param {*} source 源数组
 * @param {*} count 要取出多少项
 * @param {*} isPermutation 是否使用排列的方式
 * @return {any[]} 所有排列组合,格式为 [ [1,2], [1,3]] ...
 */
function getNumbers(source, count, isPermutation = true) {
	//如果只取一位，返回数组中的所有项，例如 [ [1], [2], [3] ]
	let currentList = source.map((item) => [item]);
	if (count === 1) {
		return currentList;
	}
	let result = [];
	//取出第一项后，再取出后面count - 1 项的排列组合，并把第一项的所有可能（currentList）和 后面count-1项所有可能交叉组合
	for (let i = 0; i < currentList.length; i++) {
		let current = currentList[i];
		//如果是排列的方式，在取count-1时，源数组中排除当前项
		let children = [];
		if (isPermutation) {
			children = getNumbers(source.filter(item => item !== current[0]), count - 1, isPermutation);
		}
		//如果是组合的方法，在取count-1时，源数组只使用当前项之后的
		else {
			children = getNumbers(source.slice(i + 1), count - 1, isPermutation);
		}
		for (let child of children) {
			result.push(current + '_' + child);
			result.push(child + '_' + current)
		}
	}
	return result;
}

module.exports = {
	request,
	getNewToken,
	loginNow,
	getNumbers,
	lnkUploadFile,
}