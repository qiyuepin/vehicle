<template>
    <div>
        <div class="footer">Copyright © tpadmin.chengzhigang.cn All Rights Reserved.</div>
    </div>
</template>

<script>
export default {
  name: "footer-bar"
}
</script>

<style lang="scss" scoped>
    .footer{
        bottom: 0;
        position: fixed;
        width: 100%;
        height: 50px;
        background: #ffffff;
        text-align: center;
        line-height: 50px;
        color: rgba(0,0,0,.45);
        border-top: 1px dashed #dcdfe6;
    }
</style>
