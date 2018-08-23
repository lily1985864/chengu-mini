import { Config } from '../utils/config.js';

class Base {
  constructor() {
    this.baseRequstUrl = Config.restUrl;
  }
  request(params) {
    var url = this.baseRequstUrl + params.url;
    if (!params.type) {
      params.type = 'GET';
    }
    wx.request({
      url: url,
      data: params.data,
      method: params.type,
      header: {
        'content-type':'application/json',
        'token':wx.getStorageSync('token')
      },
      success: function (res) {
        params.sCallback&&params.sCallback(res.data);
      },
      fail: function (err) {
        console.log(err);
      }
    })
  }

  getDataSet(event, key){
    return event.currentTarget.dataset[key];
  }
}
export {Base}