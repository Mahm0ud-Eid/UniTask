<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Firebase\JWT\JWT;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $last_login_at
 * @property string $registration_ip
 * @property string|null $api_key
 * @property string|null $language
 * @property int $is_admin
 * @property int $is_teacher
 *
 * @property Questions[] $questions
 * @property QuizAttempt[] $quizAttempts
 * @property Quizzes[] $quizzes
 * @property StudentDepartmentYear[] $studentDepartmentYears
 * @property SubjectsAccess[] $subjectsAccesses
 * @property Tasks[] $tasks
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['password_hash'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 255],
            [['registration_ip'], 'string', 'max' => 45],
            [['api_key'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 10],
            [['is_admin', 'is_teacher'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ChangeLogBehavior::class,
                'excludedAttributes' => ['auth_key', 'registration_ip'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($name)
    {
        return static::findOne(['name' => $name]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString(255);
    }

    public function generateApiKey()
    {
        $this->api_key = Yii::$app->security->generateRandomString(255);
    }

    public function isStudent()
    {
        return $this->is_admin === 0 && $this->is_teacher === 0;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // generate random values for fields other than name, email, year, and department
            $passwordHash = Yii::$app->security->generatePasswordHash('password');
            if ($this->password_hash === null) {
                $this->password_hash = $passwordHash;
            }
            if ($this->auth_key === null) {
                $this->auth_key = Yii::$app->security->generateRandomString(255);
            }
            if ($this->registration_ip === null) {
                $this->registration_ip = Yii::$app->request->userIP ?? 'None';
            }
            if ($this->api_key === null) {
                $this->api_key = Yii::$app->security->generateRandomString(255);
            }
            if ($this->language === null) {
                $this->language = 'en-US';
            }
            return true;
        }
        return false;
    }
   public function getStudentDepartmentYears()
   {
       return $this->hasOne(StudentDepartmentYear::class, ['user_id' => 'id']);
   }

    /**
     * Gets query for [[Quizzes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuizzes()
    {
        return $this->hasMany(Quizzes::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[SubjectsAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectsAccesses()
    {
        return $this->hasMany(SubjectsAccess::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['user_id' => 'id']);
    }

    public function generateScopedKey($scope, $scopeRestriction = null): string
    {
        $payload = [
            'iss' => 'BTU Tasks System',
            //'exp' => 0
            'scope' => $scope,
            'scopeRestriction' => $scopeRestriction,
            'user_id' => $this->id,
            'base_key' => md5($this->api_key),
            'issued_at' => date('Y-m-d H:i:s'),
            'issued_to' => Yii::$app->id === 'app-console' ? 'CONSOLE' : Yii::$app->request->userIP,
        ];
        return 'SCOPED ' . JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }

    public function getAvailableQuizzes(): array
    {
        $now = date('Y-m-d H:i:s');
        return Quizzes::find()
            ->where(['department_id' => $this->studentDepartmentYears->department_id,
                'semester_id' => $this->studentDepartmentYears->semester_id])
            ->andWhere(['<=', 'starts_at', $now])
            ->andWhere(['>=', 'expires_at', $now])
            ->andWhere(['active' => 1])
            ->all();
    }

    public function getAvailableTasks(): array
    {
        $now = date('Y-m-d H:i:s');
        return Tasks::find()
            ->where(['department_id' => $this->studentDepartmentYears->department_id,
                'semester_id' => $this->studentDepartmentYears->semester_id])
            ->andWhere(['<=', 'starts_at', $now])
            ->andWhere(['>=', 'ends_at', $now])
            ->andWhere(['active' => 1])
            ->all();
    }
}
