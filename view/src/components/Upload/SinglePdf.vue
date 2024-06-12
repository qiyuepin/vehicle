<template>
    <div class="pdf-uploader">
        <div v-if="pdfUrl" class="pdf-box">
            <svg-icon icon-class="pdf" class-name="pdf" />
            <span class="el-upload-list__item-actions">
            <span
                    class="el-upload-list__item-preview"
                    title="预览"
                    @click.prevent="handlePreview()">
                    <i class="el-icon-zoom-in"></i>
            </span>
            <span
                    class="el-upload-list__item-delete"
                    title="删除"
                    @click.prevent="handleRemove()">
                <i class="el-icon-delete"></i>
            </span>
      </span>
        </div>
        <el-upload
                :action="attr.action"
                :accept="attr.accept"
                :multiple="attr.multiple"
                :name="attr.fileName"
                :headers="attr.headers"
                :show-file-list="attr.showFileList"
                :on-success="handlePdfSuccess"
                title="上传PDF"
                :before-upload="beforePdfUpload">
            <i class="el-icon-plus pdf-uploader-icon"></i>
        </el-upload>
        <el-dialog :modal-append-to-body="false" top="0" class="dialogPdf" :fullscreen="true" :append-to-body="true" :visible.sync="dialogVisible">
            <iframe loading="lazy" id="pdf_container" :src="pdfUrl" frameborder="0" height="100%" width="100%"></iframe>
        </el-dialog>
    </div>
</template>
<script>
import defaultSettings from '@/settings.js'
import { getToken } from "@/utils/auth";
import { Message } from "element-ui";
import axios from 'axios'

export default {
  name: 'SinglePdf',
  props: {
    accept: {
      type: String,
      default: 'application/pdf'
    },
    fileName: {
      type: String,
      default: 'file'
    },
    drag: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      attr: {
        action: process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API + defaultSettings.uploadFileActive,
        accept: 'application/pdf',
        multiple: false,
        fileName: this.fileName,
        drag: this.drag,
        showFileList: false,
        headers: {
          'X-Access-Appid': defaultSettings.appid,
          'Authorization': 'Bearer ' + getToken()
        }
      },
      pdfUrl: '',
      dialogVisible:false,
    }
  },
  methods: {
    handlePreview(){
      this.dialogVisible = true;
    },
    handleRemove() {
      this.pdfUrl = '';
      this.$emit('input', '');
    },
    handlePdfSuccess(res, file) {
      if (res.code === 200) {
        this.initPdf(res.data.url)
        this.$emit('input', res.data.url);
      } else {
        Message({
          message: res.message || defaultSettings.errorMsg,
          type: 'error',
          duration: 5 * 1000
        })
      }
    },
    beforePdfUpload(file) {
      const isPDF = file.type === 'application/pdf'
      const isLt10M = file.size / 1024 / 1024 < 10
      if (!isPDF) {
        this.$message.error('上传文件只能是PDF格式!')
        return false
      }
      if (!isLt10M) {
        this.$message.error('上传文件大小不能超过10MB!')
        return false
      }
      return isPDF && isLt10M
    },
    initPdf(url) {
      console.log(url)
      console.log(defaultSettings.readFileActive)
      //方法一
      // let that = this
      // axios({
      //   method: 'GET',
      //   url: process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API + defaultSettings.readFileActive,
      //   params: { url:url },
      //   responseType: 'blob' // 更改responseType类型为 blob
      // }).then(res => {
      //   // 转换pdf
      //   console.log(res.data)
      //   const blob = new Blob([res.data], { type: 'application/pdf;charset=utf-8' });
      //   let windowURL = window.URL || window.webkitURL;
      //   that.pdfUrl = windowURL.createObjectURL(blob);
      // }).catch(err => {
      //   console.log(err)
      // })
      //方法二
      // this.pdfUrl = process.env.VUE_APP_BASE_URL + '/static/pdf/web/viewer.html?file=' + encodeURIComponent(url)
      this.pdfUrl = url
    },
  }
}
</script>
<style lang="scss" >
    .pdf-uploader {
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
