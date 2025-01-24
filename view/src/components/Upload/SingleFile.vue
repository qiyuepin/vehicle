<template>
    <div class="apk-uploader">
    
      <el-upload
          :action="attr.action"
          :accept="attr.accept"
          :multiple="attr.multiple"
          :name="attr.fileName"
          :headers="attr.headers"
          :show-file-list="attr.showFileList" 
          :file-list="fileList" 
          :on-success="handleFileSuccess"
          title="上传文件"
          :before-upload="beforeFileUpload">
          <el-button size="mini">上传文件</el-button>
      </el-upload>
        <!-- <el-dialog :modal-append-to-body="false" :append-to-body="true" :visible.sync="dialogVisible">
            <img width="100%" :src="imgUrl" alt="">
        </el-dialog> -->
    </div>
</template>

<script>
import defaultSettings from '@/settings.js'
import { getToken } from "@/utils/auth";
import { Message } from "element-ui";

export default {
  name: 'SingleFile',
  props: {
    accept: {
      type: String,
      default: '*/*'  // 支持所有文件
    },
    fileName: {
      type: String,
      default: 'file'  // 默认的文件字段名
    },
    drag: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      attr: {
        action: process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API + defaultSettings.uploadApkActive,
        accept: '*/*',  // 允许所有文件
        multiple: false,
        fileName: this.fileName,
        drag: this.drag,
        showFileList: false, // 显示文件列表
        headers: {
          'X-Access-Appid': defaultSettings.appid,
          'Authorization': 'Bearer ' + getToken()
        }
      },
      imgUrl: '',
      dialogVisible:false
    }
  },
  methods: {
    handlePreview(){
      this.dialogVisible = true;
    },
    handleRemove() {
      this.imgUrl = '';
      this.$emit('input', '');
    },
    handleFileSuccess(res, file) {
      if (res.code === 200) {
        console.log(res.data.url)
        // this.imgUrl = res.data.url;
        // this.$emit('input', { url: res.data.url, name: res.data.name });
        this.$emit('input', res.data.url);
        this.$emit('update:name', res.data.name);
        // this.fileList = [file];
      } else {
        Message({
          message: res.message || defaultSettings.errorMsg,
          type: 'error',
          duration: 5 * 1000
        })
      }
    },
    beforeFileUpload(file) {
      // 允许所有文件上传，取消格式和大小限制
      return true;
    }
  }
}
</script>
<style lang="scss" >
    .avatar-uploader-icon{
      width: 20px!important;
      height: 10px!important;
      font-size: 18px!important;
      line-height: 15px!important;
    }

    .apk-uploader {
        margin: 0;
        display: inline;
        vertical-align: top;
        display: inline;
        vertical-align: top;
    }

    .pdf-uploader .pdf-box {
        display: inline-block;
        height: 101px;
        width: 101px;
        position: relative;
        border: 1px dashed #d9d9d9;
        margin-right: 10px;
        text-align: center;
        vertical-align: top;
    }

    .pdf-uploader .pdf-box:hover {
        border-color: #409EFF;
    }

    .pdf-uploader :nth-child(2) {
        display: inline-block;
    }

    .pdf-uploader .el-upload-list__item-actions span {
        cursor: pointer;
        position: static;
        font-size: inherit;
        color: inherit;

    }

    .pdf-uploader .el-upload-list__item-actions span+span {
        margin-left: 15px;
    }


    .pdf-uploader .el-upload-list__item-actions:hover {
        opacity: 1;
    }

    .pdf-uploader .el-upload-list__item-actions {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        cursor: default;
        text-align: center;
        color: #fff;
        font-size: 20px;
        background-color: rgba(0, 0, 0, .5);
        transition: opacity .3s;
        opacity: 0;
    }

    .pdf-uploader .el-upload-list__item-actions:after {
        display: inline-block;
        content: "";
        height: 100%;
        vertical-align: middle;
    }

    .pdf-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .pdf-uploader .el-upload:hover {
        border-color: #409EFF;
    }

    .pdf-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 100px;
        height: 100px;
        line-height: 100px;
        text-align: center;
    }

    .svg-icon.pdf {
        width: 70%;
        height: 70%;
        margin-top: 15%;
    }
    .el-dialog__wrapper.dialogPdf{
        .el-dialog{
            .el-dialog__body{
                height: calc(100% - 30px);
            }
        }
    }
</style>